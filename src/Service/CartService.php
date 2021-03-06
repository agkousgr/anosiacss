<?php

namespace App\Service;

use App\Entity\Cart;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $authId;

    /**
     * @var string
     */
    private $kind;

    /**
     * @var string
     */
    private $domain;

    /**
     * @var string
     */
    private $appId;

    /**
     * @var string
     */
    private $companyId;

    /**
     * @var string
     */
    private $client;

    /**
     * CategoryService constructor.
     *
     * @param LoggerInterface $logger
     * @param EntityManagerInterface $em
     */
    public function __construct(LoggerInterface $logger, SessionInterface $session, $s1Credentials)
    {
        $this->logger = $logger;
        $this->session = $session;
        $this->authId = $session->get("authID");
        $this->kind = $s1Credentials['kind'];
        $this->domain = $s1Credentials['domain'];
        $this->appId = $s1Credentials['appId'];
        $this->companyId = $s1Credentials['companyId'];
        $this->client = new \SoapClient('https://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);
    }

    /**
     * @param $id
     * @param $authId
     * @param $pagesize
     * @param $cartArr
     * @return array
     * @throws \Exception
     */
    public function getCartItems($ids, $cartArr, $pagesize, $highPrice = -1)
    {
        $pagesize = ($pagesize !== 1) ? $pagesize : 2;
        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetItemsRequest>
    <Type>1005</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <pagesize>$pagesize</pagesize>
    <pagenumber>0</pagenumber>
    <ItemID>$ids</ItemID>
    <ItemCode>null</ItemCode>
    <IsSkroutz>-1</IsSkroutz>
    <SearchToken>null</SearchToken>
    <OrderBy>null</OrderBy>
    <MakeID>null</MakeID>
    <ManufacturID>null</ManufacturID>
    <LowPrice>-1</LowPrice>
    <HighPrice>$highPrice</HighPrice>
    <WebVisible>-1</WebVisible>
    <IsActive>-1</IsActive>
    <UpdateDT>2000-01-01T00:00:00</UpdateDT>
</ClientGetItemsRequest>
EOF;
        try {
            $result = $this->client->SendMessage(['Message' => $message]);
            $items = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            $itemsArr = $this->initializeProducts($items->GetDataRows->GetItemsRow, $cartArr);
            return $itemsArr;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * @param $products
     * @param $cartArr
     * @return array
     * @throws \Exception
     */
    private function initializeProducts($products, $cartArr)
    {
        try {
            $prArr = array();
            $subTotal = 0;
            $i = 0;
            foreach ($products as $pr) {
                if ($pr->WebVisible) {
                    $subTotal += $pr->WebPrice;
                    $prArr[] = array(
                        'id' => $pr->ID,
                        'name' => $pr->Name2,
                        'isVisible' => $pr->WebVisible,
                        'retailPrice' => $pr->RetailPrice,
                        'slug' => $pr->Slug,
                        'discount' => $pr->Discount,
                        'webDiscount' => $pr->WebDiscountPerc,
                        'webPrice' => ($pr->Discount) ? round((floatval($pr->RetailPrice) * (100 - floatval($pr->Discount)) / 100), 2) : 0,
                        'outOfStock' => $pr->OutOfStock,
                        'remainNotReserved' => $pr->RemainNotReserved,
                        'volumeWeight' => $pr->VolumeWeight,
                        'webFree' => $pr->WebFree,
                        'overAvailability' => $pr->OverAvailability,
                        'maxByOrder' => $pr->MaxByOrder,
                        'hasMainImage' => $pr->HasMainPhoto,
                        'imageUrl' => ($pr->HasMainPhoto) ? 'https://caron.cloudsystems.gr/FOeshopAPIWeb/DF.aspx?' . str_replace('[Serial]', '01102472475217', str_replace('&amp;', '&', $pr->MainPhotoUrl)) : '',
                        'cartSubTotal' => $subTotal,
                        'cartId' => $cartArr[$i]->getId(),
                        'quantity' => $cartArr[$i]->getQuantity()
                    );
                    $i++;
                }
            }
//            'manufacturer' => $pr->ManufactorName
//            return new Response(dump(print_r($this->prCategories)));
            return $prArr;
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function checkCoupon($voucherId = -1, $voucherCode = 'null')
    {
        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetVoucherRequest>
    <Type>1072</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <VoucherID>$voucherId</VoucherID>
    <Code>$voucherCode</Code>
</ClientGetVoucherRequest>
EOF;
        try {
            $result = $this->client->SendMessage(['Message' => $message]);
            $items = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));

            if ($items->GetDataRows->GetVoucherRow) {
                $fromDt = new \DateTime($items->GetDataRows->GetVoucherRow->FromDT);
                $toDt = new \DateTime($items->GetDataRows->GetVoucherRow->ToDT);
                $today = new \DateTime();
                $today->setTimezone(new \DateTimeZone('Europe/Athens'));
                if ($fromDt > $today || $today > $toDt) {
                    return false;
                }
                //Todo: put all coupon session in one variable
                if (intval($items->RowsCount) > 0) {
                    $this->session->set('couponDisc', intval($items->GetDataRows->GetVoucherRow->Value));
                    $this->session->set('couponDiscPerc', intval($items->GetDataRows->GetVoucherRow->Percentage));
                    $this->session->set('couponName', $voucherCode);
                    $this->session->set('couponId', intval($items->GetDataRows->GetVoucherRow->ID));

                    return true;
                } else {
                    return false;
                }
            }
            return false;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * Append session cart items to user's cart
     *
     * @param $em
     * @param $username
     *
     * @throws \Exception
     */
    public function assignSessionToUserCart($em, $username)
    {
        try {
            $cartItems = $em->getRepository(Cart::class)->getCartBySession($this->session->getId());
            if ($cartItems) {
                foreach ($cartItems as $item) {
                    $cartItem = $em->getRepository(Cart::class)->findOneBy(['product' => $item->getProduct()->getId(), 'username' => $username]);
                    if ($cartItem) {
                        $cartItem->setQuantity($item->getQuantity());
                        $em->persist($cartItem);
                        $em->remove($item);
                        $em->flush();
                    } else {
                        $item->setUsername($username);
                        $em->persist($item);
                        $em->flush();
                    }
                }
            }

        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function clearCart($em, $username)
    {
        try {
            $cartItems = $em->getRepository(Cart::class)->getCartByUser($username);
            if ($cartItems) {
                foreach ($cartItems as $item) {
                    $item->setSessionId('');
                    $em->persist($item);
                    $em->flush();
                }
            }

        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

}