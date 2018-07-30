<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 17/4/2018
 * Time: 1:38 πμ
 */

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ProductService
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     * @param SessionInterface $session
     */
    private $authId;

    public function __construct(LoggerInterface $logger, SessionInterface $session)
    {
        $this->logger = $logger;
        $this->session = $session;
        $this->authId = $session->get("authID");
    }

    /**
     * @param $ctgId
     * @param $authId
     * @return array
     * @throws \Exception
     */
    public function getCategoryItems($ctgId)
    {
        $client = new \SoapClient('https://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetCategoryItemsRequest>
    <Type>1011</Type>
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
    <pagesize>10</pagesize>
    <pagenumber>0</pagenumber>
    <CategoryID>$ctgId</CategoryID>
    <SearchToken>null</SearchToken>
    <IncludeChildCategories>1</IncludeChildCategories>
</ClientGetCategoryItemsRequest>
EOF;
        try {
            $itemsArr = array();
            $result = $client->SendMessage(['Message' => $message]);
            $items = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            if ($items !== false) {
                $itemsArr = $this->initializeProducts($items->GetDataRows->GetCategoryItemsRow);
            }
            return $itemsArr;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * @param $ctgId
     * @return int
     */
    public function getCategoryItemsCount($ctgId)
    {
        $client = new \SoapClient('https://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetCategoryItemsCountRequest>
    <Type>1011</Type>
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
    <pagesize>1000</pagesize>
    <pagenumber>0</pagenumber>
    <CategoryID>$ctgId</CategoryID>
    <SearchToken>null</SearchToken>
    <IncludeChildCategories>1</IncludeChildCategories>
</ClientGetCategoryItemsCountRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $items = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            dump($message, $result);
            return (int)$items->GetDataRows->GetItemsCountRow->Count;
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
                if ((string)$pr->WebVisible !== "false") {
                    $prArr[] = array(
                        'id' => $pr->ID,
                        'name' => $pr->Name2,
                        'summary' => $pr->SmallDescription,
                        'body' => $pr->LargeDescription,
                        'extraInfo' => $pr->InstructionsHTML,
                        'isVisible' => $pr->WebVisible,
                        'retailPrice' => $pr->RetailPrice,
                        'discount' => $pr->WebDiscountPerc,
                        'webPrice' => $pr->WebPrice,
                        'outOfStock' => $pr->OutOfStock,
                        'remainNotReserved' => $pr->Remain,
                        'webFree' => $pr->WebFree,
                        'overAvailability' => $pr->OverAvailability,
                        'maxByOrder' => $pr->MaxByOrder,
                        'hasMainImage' => $pr->HasMainPhoto,
                        'imageUrl' => ($pr->HasMainPhoto) ? 'https://caron.cloudsystems.gr/FOeshopAPIWeb/DF.aspx?' . str_replace('[Serial]', '01102459200617', str_replace('&amp;', '&', $pr->MainPhotoUrl)) : ''
                    );
                }
            }
            return $prArr;
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }


    /**
     * @param $id
     * @param $authId
     * @param $keyword
     * @return array
     * @throws \Exception
     */
    public function getItems($id, $keyword = 'null')
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
    <ItemID>$id</ItemID>
    <ItemCode>null</ItemCode>
    <SearchToken>$keyword</SearchToken>
</ClientGetItemsRequest>
EOF;
        try {
            $itemsArr = array();
            $result = $client->SendMessage(['Message' => $message]);
            $items = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            dump($message, $result);
            if ($items !== false && $keyword !== 'null') {
                $itemsArr = $this->initializeProducts($items->GetDataRows->GetItemsRow);
            } else {
                $itemsArr = $this->initializeProduct($items->GetDataRows->GetItemsRow);
            }
            return $itemsArr;
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
            if ((string)$pr->WebVisible !== 'false') {
                $prArr = array(
                    'id' => $pr->ID,
                    'name' => $pr->Name2,
                    'summary' => $pr->SmallDescription,
                    'body' => $pr->LargeDescription,
                    'extraInfo' => $pr->InstructionsHTML,
                    'retailPrice' => $pr->RetailPrice,
                    'discount' => $pr->WebDiscountPerc,
                    'mainBarcode' => $pr->MainBarcode,
                    'isVisible' => $pr->WebVisible,
                    'webPrice' => $pr->WebPrice,
                    'outOfStock' => $pr->OutOfStock,
                    'remainNotReserved' => $pr->Remain,
                    'webFree' => $pr->WebFree,
                    'overAvailability' => $pr->OverAvailability,
                    'maxByOrder' => $pr->MaxByOrder,
                    'hasMainImage' => $pr->HasMainPhoto,
                    'imageUrl' => ($pr->HasMainPhoto) ? 'https://caron.cloudsystems.gr/FOeshopAPIWeb/DF.aspx?' . str_replace('[Serial]', '01102459200617', str_replace('&amp;', '&', $pr->MainPhotoUrl)) : ''
                );
            }
//            'manufacturer' => $pr->ManufactorName
//            return new Response(dump(print_r($this->prCategories)));
            return $prArr;
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * @param $id
     * @param string $keyword
     * @return int
     */
    public function getItemsCount($id, $keyword = 'null')
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
    <pagesize></pagesize>
    <pagenumber>0</pagenumber>
    <ItemID>$id</ItemID>
    <ItemCode>null</ItemCode>
    <SearchToken>$keyword</SearchToken>
</ClientGetItemsRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $items = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            dump($message, $result);
            return (int)$items->GetDataRows->GetItemsCountRow->Count;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }
}