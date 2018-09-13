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
     * @var SessionInterface
     */
    private $session;

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
//        dump($this->authId);
    }

    /**
     * @param $ctgId
     * @param $sortBy
     * @param $makeId
     * @return array
     * @throws \Exception
     */
    public function getCategoryItems($ctgId, $page, $pagesize, $sortBy = 'NameAsc', $makeId = 'null')
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
    <pagesize>$pagesize</pagesize>
    <pagenumber>$page</pagenumber>
    <CategoryID>$ctgId</CategoryID>
    <MakeID>$makeId</MakeID>
    <SearchToken>null</SearchToken>
    <OrderBy></OrderBy>
    <IncludeChildCategories>1</IncludeChildCategories>
</ClientGetCategoryItemsRequest>
EOF;
        try {
            $itemsArr = array();
            $result = $client->SendMessage(['Message' => $message]);
            $items = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            dump($message, $items);
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
    <Type>1040</Type>
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
    <pagesize>1000</pagesize>
    <pagenumber>0</pagenumber>
    <CategoryID>$ctgId</CategoryID>
    <SearchToken>null</SearchToken>
    <OrderBy></OrderBy>
<IncludeChildCategories>1</IncludeChildCategories>
</ClientGetCategoryItemsCountRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $items = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
//            dump($message, $result);
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
                        'summary' => $pr->SmallDescriptionHTML,
                        'body' => $pr->LargeDescriptionHTML,
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
//            dump($prArr);
            return $prArr;
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }


    /**
     * @param $id
     * @param $keyword
     * @param $pagesize
     * @param $sortBy
     * @param $isSkroutz
     * @return array
     * @throws \Exception
     */
    public function getItems($id = 'null', $keyword = 'null', $pagesize, $sortBy = 'null', $isSkroutz = -1, $makeId = 'null')
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
    <pagesize>$pagesize</pagesize>
    <pagenumber>0</pagenumber>
    <ItemID>$id</ItemID>
    <ItemCode>null</ItemCode>
    <MakeID>$makeId</MakeID>
    <IsSkroutz>$isSkroutz</IsSkroutz>
    <SearchToken>$keyword</SearchToken>
    <OrderBy>$sortBy</OrderBy>
</ClientGetItemsRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $items = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            dump($items);
            if ($items !== false && ($keyword !== 'null' || $makeId !== 'null' || $isSkroutz === '1')) {
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
            $prArr = [];
            if ((string)$pr->WebVisible !== 'false') {
                $prArr = array(
                    'id' => $pr->ID,
                    'name' => $pr->Name2,
                    'summary' => strip_tags($pr->SmallDescriptionHTML),
                    'body' => strip_tags($pr->LargeDescriptionHTML),
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
            $imagesArr = $this->getItemPhotos($pr->ID);

//            'manufacturer' => $pr->ManufactorName
//            return new Response(dump(print_r($this->prCategories)));
            return array_merge($prArr, $imagesArr);
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    private function getItemPhotos($id)
    {
        $client = new \SoapClient('https://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetItemPhotosRequest>
    <Type>1044</Type>
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
    <pagesize>10</pagesize>
    <pagenumber>0</pagenumber>
    <ItemID>$id</ItemID>
    <ItemCode>null</ItemCode>
    
</ClientGetItemPhotosRequest>
EOF;
        try {
            $imagesArr = array();
            $result = $client->SendMessage(['Message' => $message]);
            $items = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            dump($result);
            if (intval($items->RowsCount) > 0) {
                $imagesArr = $this->initializeImages($items->GetDataRows->GetItemPhotosRow);
            }
            return $imagesArr;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * @param $images
     * @return array
     * @throws \Exception
     */
    private function initializeImages($images)
    {
        try {
            $imagesArr = [];
            foreach ($images as $image) {
                $imagesArr['extraImages'][] = array(
                    'id' => $image->ID,
                    'name' => $image->PhotoFileName,
                    'isMain' => $image->IsMain,
                    'imageUrl' => 'https://caron.cloudsystems.gr/FOeshopAPIWeb/DF.aspx?' . str_replace('[Serial]', '01102459200617', str_replace('&amp;', '&', $image->PhotoUrl))
                );
                dump($image);
            }
            return $imagesArr;
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * @param $id
     * @param $keyword
     * @param $pagesize
     * @param $sortBy
     * @param $isSkroutz
     *
     * @return int
     */
    public function getItemsCount($id, $keyword = 'null', $isSkroutz = -1, $makeId = 'null')
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
    <MakeID>$makeId</MakeID>
    <IsSkroutz>$isSkroutz</IsSkroutz>
    <SearchToken>$keyword</SearchToken>
    <OrderBy></OrderBy>
</ClientGetItemsRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $items = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            return (int)$items->GetDataRows->GetItemsCountRow->Count;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }


}