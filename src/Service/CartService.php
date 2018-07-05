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
     * CategoryService constructor.
     *
     * @param LoggerInterface $logger
     * @param EntityManagerInterface $em
     */
    public function __construct(LoggerInterface $logger, SessionInterface $session)
    {
        $this->logger = $logger;
//        $this->em = $em;
        $this->session = $session;
        $this->authId = $session->get("authID");
    }

    /**
     * @param $id
     * @param $authId
     * @param $cartArr
     * @return array
     * @throws \Exception
     */
    public function getCartItems($ids, $cartArr)
    {
        $client = new \SoapClient('https://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetItemsRequest>
    <Type>1005</Type>
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
    <pagesize>10</pagesize>
    <pagenumber>0</pagenumber>
    <ItemID>$ids</ItemID>
    <ItemCode>null</ItemCode>
    <SearchToken>null</SearchToken>
</ClientGetItemsRequest>
EOF;
        try {
            $itemsArr = array();
            $result = $client->SendMessage(['Message' => $message]);
            $items = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            dump($items);
            if ($items !== false) {
                $itemsArr = $this->initializeProducts($items->GetDataRows->GetItemsRow, $cartArr);
            }
            dump($itemsArr);

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
                $subTotal +=  $pr->WebPrice;
                $prArr[] = array(
                    'id' => $pr->ID,
                    'name' => $pr->Name2,
                    'retailPrice' => $pr->RetailPrice,
                    'discount' => $pr->WebDiscountPerc,
                    'mainBarcode' => $pr->MainBarcode,
                    'isVisible' => $pr->WebVisible,
                    'webPrice' => $pr->WebPrice,
                    'outOfStock' => $pr->OutOfStock,
                    'remainNotReserved' => $pr->Remain,
                    'webFree' => $pr->WebFree,
                    'hasMainImage' => $pr->HasMainPhoto,
                    'imageUrl' => ($pr->HasMainPhoto) ? 'https://caron.cloudsystems.gr/FOeshopAPIWeb/DF.aspx?' . str_replace('[Serial]', '01102459200617', str_replace('&amp;', '&', $pr->MainPhotoUrl)) : '',
                    'cartSubTotal' => $subTotal,
                    'cartId' => $cartArr[$i]->getId(),
                    'quantity' => $cartArr[$i]->getQuantity()
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