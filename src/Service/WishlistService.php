<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 17/6/2018
 * Time: 10:47 μμ
 */

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class WishlistService
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
     * CategoryService constructor.
     *
     * @param LoggerInterface $logger
     * @param EntityManagerInterface $em
     */
    public function __construct(LoggerInterface $logger, SessionInterface $session, $s1Credentials)
    {
        $this->logger = $logger;
//        $this->em = $em;
        $this->session = $session;
        $this->authId = $session->get("authID");
        $this->kind = $s1Credentials['kind'];
        $this->domain = $s1Credentials['domain'];
        $this->appId = $s1Credentials['appId'];
        $this->companyId = $s1Credentials['companyId'];
    }

    /**
     * @param $id
     * @param $authId
     * @param $wishlistArr
     * @return array
     * @throws \Exception
     */
    public function getWishlistItems($ids, $wishlistArr)
    {
        $client = new \SoapClient('https://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetItemsRequest>
    <Type>1005</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <pagesize>10</pagesize>
    <pagenumber>0</pagenumber>
    <ItemID>$ids</ItemID>
    <ItemCode>null</ItemCode>
    <IsSkroutz>-1</IsSkroutz>
    <SearchToken>null</SearchToken>
    <OrderBy>null</OrderBy>
    <MakeID>null</MakeID>
    <ManufacturID>null</ManufacturID>
    <LowPrice>-1</LowPrice>
    <HighPrice>-1</HighPrice>
    <WebVisible>-1</WebVisible>
    <IsActive>-1</IsActive>
</ClientGetItemsRequest>
EOF;
        try {
            $itemsArr = array();
            $result = $client->SendMessage(['Message' => $message]);
            $items = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            if ($items !== false) {
                $itemsArr = $this->initializeProducts($items->GetDataRows->GetItemsRow, $wishlistArr);
            }

            return $itemsArr;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * @param $products
     * @param $wishlistArr
     * @return array
     * @throws \Exception
     */
    private function initializeProducts($products, $wishlistArr)
    {
        try {
            $prArr = array();
            $subTotal = 0;
            $i = 0;
            foreach ($products as $pr) {
                $subTotal +=  $pr->WebPrice;
                $prArr[] = array(
                    'id' => $pr->ID,
                    'name' => $pr->Name2,
                    'isVisible' => $pr->WebVisible,
                    'retailPrice' => $pr->RetailPrice,
                    'discount' => $pr->Discount,
                    'webDiscount' => $pr->WebDiscountPerc,
                    'webPrice' => ($pr->Discount) ? round((floatval($pr->RetailPrice) * (100 - floatval($pr->Discount))/100), 2) : 0,
                    'outOfStock' => $pr->OutOfStock,
                    'remainNotReserved' => $pr->RemainNotReserved,
                    'webFree' => $pr->WebFree,
                    'overAvailability' => $pr->OverAvailability,
                    'maxByOrder' => $pr->MaxByOrder,
                    'hasMainImage' => $pr->HasMainPhoto,
                    'imageUrl' => ($pr->HasMainPhoto) ? 'https://caron.cloudsystems.gr/FOeshopAPIWeb/DF.aspx?' . str_replace('[Serial]', '01102472475217', str_replace('&amp;', '&', $pr->MainPhotoUrl)) : '',
                    'wishlistId' => $wishlistArr[$i]->getId()
                );
                $i++;
            }
//            'manufacturer' => $pr->ManufactorName
//            return new Response(dump(print_r($this->prCategories)));
            return $prArr;
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}