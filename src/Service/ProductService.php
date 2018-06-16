<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 17/4/2018
 * Time: 1:38 πμ
 */

namespace App\Service;

use Psr\Log\LoggerInterface;

class ProductService
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param $ctgId
     * @param $authId
     * @return array
     * @throws \Exception
     */
    public function getProducts($ctgId, $authId)
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetCategoryItemsRequest>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
    <Type>1011</Type>
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <AuthID>$authId</AuthID>
    <CategoryID>$ctgId</CategoryID>
</ClientGetCategoryItemsRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
//            return $result->SendMessageResult;
//            return str_replace("utf-16", "utf-8", $result->SendMessageResult);
            $items = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            dump($result);
            $itemsArr = $this->initializeProducts($items->GetDataRows->GetCategoryItemsRow);
            return $itemsArr;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * @param $products
     * @return array
     * @throws \Exception
     */
    private function initializeProducts($products)
    {
        try {
            $prArr = array();
            foreach ($products as $pr) {
                $prArr[] = array(
                    'id' => $pr->ID,
                    'name' => $pr->Name2,
                    'isVisible' => $pr->WebVisible,
                    'retailPrice' => $pr->RetailPrice,
                    'discount' => $pr->WebDiscountPerc,
                    'webPrice' => $pr->WebPrice,
                    'outOfStock' => $pr->OutOfStock,
                    'remainNotReserved' => $pr->Remain,
                    'webFree' => $pr->WebFree
                );
            }
//            array_multisort(array_column($prArr, "priority"), $prArr);
//            return new Response(dump(print_r($this->prCategories)));
            return $prArr;
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * @param $id
     * @param $authId
     * @return array
     * @throws \Exception
     */
    public function getProduct($id, $authId)
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetItemsRequest>
    <Type>1005</Type>
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <AuthID>$authId</AuthID>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
    <pagesize>10</pagesize>
    <pagenumber>0</pagenumber>
    <ItemID>$id</ItemID>
    <ItemCode>null</ItemCode>
</ClientGetItemsRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
//            return $result->SendMessageResult;
//            return str_replace("utf-16", "utf-8", $result->SendMessageResult);
            $item = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            dump($item);
            $itemArr = $this->initializeProduct($item->GetDataRows->GetItemsRow);
            return $itemArr;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * @param $pr
     * @return array
     * @throws \Exception
     */
    private function initializeProduct($pr)
    {
        try {
            $prArr = array(
                'id' => $pr->ID,
                'name' => $pr->Name2,
                'retailPrice' => $pr->RetailPrice,
                'discount' => $pr->WebDiscountPerc,
                'isVisible' => $pr->WebVisible,
                'webPrice' => $pr->WebPrice,
                'outOfStock' => $pr->OutOfStock,
                'remainNotReserved' => $pr->Remain,
                'webFree' => $pr->WebFree
            );
//            'manufacturer' => $pr->ManufactorName
//            return new Response(dump(print_r($this->prCategories)));
            return $prArr;
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}