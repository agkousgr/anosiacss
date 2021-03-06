<?php

namespace App\Service;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
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
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var string
     *
     * @param SessionInterface $session
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
     * @var \SoapClient
     */
    private $client;

    public function __construct(LoggerInterface $logger, EntityManagerInterface $em, SessionInterface $session, $s1Credentials)
    {
        $this->logger = $logger;
        $this->em = $em;
        $this->session = $session;
        $this->authId = $this->session->get("authID");
        $this->kind = $s1Credentials['kind'];
        $this->domain = $s1Credentials['domain'];
        $this->appId = $s1Credentials['appId'];
        $this->companyId = $s1Credentials['companyId'];
        $this->client = new \SoapClient('https://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);
    }

    /**
     * @param $ctgId
     * @param $page
     * @param $pagesize
     * @param $sortBy
     * @param $makeId
     *
     * @return array
     * @throws \Exception
     */
    public function getCategoryItems($ctgId, $page, $pagesize, $sortBy = 'NameAsc', $makeId = 'null', $priceRange = 'null', $webVisible = 1, $manufacturerId = 'null')
    {

        $priceRangeArr = ($priceRange != 'null') ? explode('-', $priceRange) : -1;
        $lowPrice = ($priceRangeArr === -1) ? -1 : $priceRangeArr[0];
        $highPrice = ($priceRangeArr === -1) ? -1 : $priceRangeArr[1];

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetCategoryItemsRequest>
    <Type>1011</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <pagesize>$pagesize</pagesize>
    <pagenumber>$page</pagenumber>
    <CategoryID>$ctgId</CategoryID>
    <SearchToken>null</SearchToken>
    <IncludeChildCategories>1</IncludeChildCategories>
    <OrderBy>$sortBy</OrderBy>
    <MakeID>$makeId</MakeID>
    <ManufacturID>$manufacturerId</ManufacturID>
    <LowPrice>$lowPrice</LowPrice>
    <HighPrice>$highPrice</HighPrice>
    <IsVisibleCategory>-1</IsVisibleCategory>
    <WebVisible>$webVisible</WebVisible>
    <IsActive>1</IsActive>  
    <IsSuggested>-1</IsSuggested>
    <JoinedCategories>0</JoinedCategories>  
</ClientGetCategoryItemsRequest>
EOF;
        try {
            $itemsArr = [];
            $result = $this->client->SendMessage(['Message' => $message]);
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
     * @param $makeId
     *
     * @return int
     */
    public function getCategoryItemsCount($ctgId, $makeId = 'null', $priceRange = 'null', $webVisible = 1, $manufacturerId = 'null')
    {

        $priceRangeArr = ($priceRange != 'null') ? explode('-', $priceRange) : -1;
        $lowPrice = ($priceRangeArr === -1) ? -1 : $priceRangeArr[0];
        $highPrice = ($priceRangeArr === -1) ? -1 : $priceRangeArr[1];

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetCategoryItemsCountRequest>
    <Type>1040</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <pagesize>100</pagesize>
    <pagenumber>0</pagenumber>
    <CategoryID>$ctgId</CategoryID>
    <SearchToken>null</SearchToken>
    <IncludeChildCategories>1</IncludeChildCategories>
    <MakeID>$makeId</MakeID> 
    <ManufacturID>$manufacturerId</ManufacturID>
    <LowPrice>$lowPrice</LowPrice>
    <HighPrice>$highPrice</HighPrice>
    <IsVisibleCategory>-1</IsVisibleCategory>
    <WebVisible>$webVisible</WebVisible>
    <IsActive>-1</IsActive>  
    <IsSuggested>-1</IsSuggested>
    <JoinedCategories>0</JoinedCategories>
</ClientGetCategoryItemsCountRequest>
EOF;
        try {
            $result = $this->client->SendMessage(['Message' => $message]);
            $items = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
//            dump($message, $result);
            return $items->GetDataRows->GetCategoryItemsCountRow;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * @param $products
     *
     * @return array
     * @throws \Exception
     */
    private function initializeProducts($products)
    {
        try {
            $prArr = [];
            foreach ($products as $pr) {
//                if (strval($pr->WebVisible) !== "false" && strval($pr->Slug) !== '') {
                    $mainPhoto = (strval($pr->HasMainPhoto) !== 'false') ? explode('=', $pr->MainPhotoUrl) : [];
                    $prArr[] = [
                        'id' => $pr->ID,
                        'name' => $pr->Name2,
                        'slug' => $pr->Slug,
                        'summary' => $pr->SmallDescriptionHTML,
                        'body' => $pr->LargeDescriptionHTML,
                        'extraInfo' => $pr->InstructionsHTML,
                        'deliveryInfo' => $pr->DeliveryHTML,
                        'isVisible' => $pr->WebVisible,
                        'prCode' => $pr->Code,
                        'retailPrice' => $pr->RetailPrice,
                        'discount' => $pr->Discount,
                        'mainBarcode' => $pr->MainBarcode,
                        'webDiscount' => $pr->WebDiscountPerc,
                        'webPrice' => ($pr->Discount) ? round((floatval($pr->RetailPrice) * (100 - floatval($pr->Discount)) / 100), 2) : 0,
                        'outOfStock' => $pr->OutOfStock,
                        'volumeWeight' => $pr->VolumeWeight,
                        'manufacturerId' => $pr->ManufacturID,
                        'remainNotReserved' => $pr->RemainNotReserved,
                        'isNew' => $this->checkIfProductIsNew($pr->InsertDT),
                        'webFree' => $pr->WebFree,
                        'webVisible' => $pr->WebVisible,
                        'active' => $pr->IsActive,
                        'overAvailability' => $pr->OverAvailability,
                        'maxByOrder' => $pr->MaxByOrder,
                        'hasMainImage' => $pr->HasMainPhoto,
                        'categories' => $pr->AllCategoryIDs,
                        'imageUrl' => (empty($mainPhoto)) ? '' : 'images/products/FOSO/01102459200217/1001/mtrl/51/-/' . $pr->ID . '/' . end($mainPhoto)
//                        'imageUrl' => (strval($pr->HasMainPhoto) !== 'false') ? 'https://caron.cloudsystems.gr/FOeshopAPIWeb/DF.aspx?' . str_replace('[Serial]', '01102472475217', str_replace('&amp;', '&', $pr->MainPhotoUrl)) : ''
                    ];
//                }
            }

            return $prArr;
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    private function checkIfProductIsNew($insertDate)
    {
        $prDateArr = explode('T', $insertDate);
        $prDate = new \DateTime($prDateArr[0]);
        $curDate = new \DateTime();

        return (date_diff($curDate, $prDate)->days < 20) ? 'new' : '';
    }


    /**
     * @param $id
     * @param $keyword
     * @param $pagesize
     * @param $sortBy
     * @param $isSkroutz
     * @param $makeId
     * @param $priceRange
     *
     * @return array
     * @throws \Exception
     */
    public function getItems($id = 'null', $keyword = 'null', $pagesize, $sortBy = 'NameAsc', $isSkroutz = -1, $makeId = 'null', $priceRange = 'null', $itemCode = 'null', $webVisible = 1, $manufacturerId = 'null', $page = 0, $updatedDate = '2000-01-01T00:00:00')
    {

        $priceRangeArr = ($priceRange != 'null') ? explode('-', $priceRange) : -1;
        $lowPrice = ($priceRangeArr === -1) ? -1 : $priceRangeArr[0];
        $highPrice = ($priceRangeArr === -1) ? -1 : $priceRangeArr[1];

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
    <pagenumber>$page</pagenumber>
    <ItemID>$id</ItemID>
    <ItemCode>$itemCode</ItemCode>
    <MakeID>$makeId</MakeID>
    <ManufacturID>$manufacturerId</ManufacturID>
    <IsSkroutz>$isSkroutz</IsSkroutz>
    <SearchToken>$keyword</SearchToken>
    <OrderBy>$sortBy</OrderBy>
    <LowPrice>$lowPrice</LowPrice>
    <HighPrice>$highPrice</HighPrice>
    <WebVisible>$webVisible</WebVisible>
    <IsActive>1</IsActive>
    <UpdateDT>$updatedDate</UpdateDT>
</ClientGetItemsRequest>
EOF;
        try {
            $itemsArr = [];
            $result = $this->client->SendMessage(['Message' => $message]);
            $items = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult), 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_PARSEHUGE);
            if (intval($items->RowsCount) > 0) {
                if ($items !== false && ($keyword !== 'null' || $makeId !== 'null' || $isSkroutz === '1' || ($id === 'null' && $itemCode === 'null'))) { // THIS IS FOR MIGRATING PRODUCTS
//                if ($items !== false && ($keyword !== 'null' || $makeId !== 'null' || $isSkroutz === '1')) {
                    $itemsArr = $this->initializeProducts($items->GetDataRows->GetItemsRow);
                } else if ($items !== false) {
                    $itemsArr = $this->initializeProduct($items->GetDataRows->GetItemsRow);
                }
            }

            return $itemsArr;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }


    public function getItemsCount($keyword = 'null', $makeId = 'null', $priceRange = 'null', $webVisible = 1, $manufacturerId = 'null')
    {

        $priceRangeArr = ($priceRange != 'null') ? explode('-', $priceRange) : -1;
        $lowPrice = ($priceRangeArr === -1) ? -1 : $priceRangeArr[0];
        $highPrice = ($priceRangeArr === -1) ? -1 : $priceRangeArr[1];

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetItemsCountRequest>
    <Type>1038</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <pagesize>100</pagesize>
    <pagenumber>0</pagenumber>
    <ItemID>null</ItemID>
    <ItemCode>null</ItemCode>
    <MakeID>$makeId</MakeID>
    <ManufacturID>$manufacturerId</ManufacturID>
    <IsSkroutz>-1</IsSkroutz>
    <SearchToken>$keyword</SearchToken>
    <OrderBy>null</OrderBy>
    <LowPrice>$lowPrice</LowPrice>
    <HighPrice>$highPrice</HighPrice>
    <WebVisible>$webVisible</WebVisible>
    <IsActive>1</IsActive>  
</ClientGetItemsCountRequest>
EOF;

        try {
            $result = $this->client->SendMessage(['Message' => $message]);
            $items = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            return (int)$items->GetDataRows->GetItemsCountRow->Count;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }

    }

    /**
     * @param $pr
     *
     * @return array
     * @throws \Exception
     */
    private function initializeProduct($pr)
    {
        try {
            $prArr = [];
            $imagesArr = [];
            if ((string)$pr->WebVisible !== 'false') {
                $mainPhoto = (strval($pr->HasMainPhoto) !== 'false') ? explode('=', $pr->MainPhotoUrl) : [];
                $prArr = [
                    'id' => $pr->ID,
                    'name' => $pr->Name2,
                    'slug' => $pr->Slug,
                    'summary' => strip_tags($pr->SmallDescriptionHTML),
                    'body' => $pr->LargeDescriptionHTML,
                    'instructions' => $pr->InstructionsHTML,
                    'ingredients' => $pr->IngredientsHTML,
                    'deliveryInfo' => $pr->DeliveryHTML,
                    'seoTitle' => $pr->SEOTitle,
                    'seoKeywords' => $pr->SEOKeywords,
                    'retailPrice' => $pr->RetailPrice,
                    'discount' => $pr->Discount,
                    'mainBarcode' => $pr->MainBarcode,
                    'prCode' => $pr->Code,
                    'isVisible' => $pr->WebVisible,
                    'webPrice' => ($pr->Discount) ? round((floatval($pr->RetailPrice) * (100 - floatval($pr->Discount)) / 100), 2) : 0,
                    'webDiscount' => $pr->WebDiscountPerc,
                    'isNew' => $this->checkIfProductIsNew($pr->InsertDT),
                    'outOfStock' => $pr->OutOfStock,
                    'remainNotReserved' => $pr->RemainNotReserved,
                    'volumeWeight' => $pr->VolumeWeight,
                    'manufacturerId' => $pr->ManufacturID,
                    'webFree' => $pr->WebFree,
                    'overAvailability' => $pr->OverAvailability,
                    'maxByOrder' => $pr->MaxByOrder,
                    'hasMainImage' => $pr->HasMainPhoto,
                    'imageUrl' => (empty($mainPhoto)) ? '' : 'images/products/FOSO/01102459200217/1001/mtrl/51/-/' . $pr->ID . '/' . end($mainPhoto),
                    'categories' => $this->getProductCategories($pr->AllCategoryIDs)
//                    'categories' => $pr->AllCategoryIDs
                ];
                $imagesArr = $this->getItemPhotos($pr->ID);
            }
//            'manufacturer' => $pr->ManufactorName
//            return new Response(dump(print_r($this->prCategories)));
            return array_merge($prArr, $imagesArr);
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    private function getProductCategories($ctgArr)
    {
        $categories = $this->em->getRepository(Category::class)->findBy(['s1id' => $ctgArr]);

        return $categories;
    }

    public function getItemPhotos($id)
    {

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetItemPhotosRequest>
    <Type>1044</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <pagesize>10</pagesize>
    <pagenumber>0</pagenumber>
    <ItemID>$id</ItemID>
    <ItemCode>null</ItemCode>
</ClientGetItemPhotosRequest>
EOF;
        try {
            $imagesArr = [];
            $result = $this->client->SendMessage(['Message' => $message]);
            $items = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
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
     *
     * @return array
     * @throws \Exception
     */
    private function initializeImages($images)
    {
        try {
            $imagesArr = [];
            foreach ($images as $image) {
                $mainPhoto = (strval($image->PhotoUrl) !== 'false') ? explode('=', $image->PhotoUrl) : [];
                $imagesArr['extraImages'][] = [
                    'id' => $image->ID,
                    'name' => $image->PhotoFileName,
                    'isMain' => $image->IsMain,
                    'imageUrl' => (empty($mainPhoto)) ? '' : 'images/products/FOSO/01102459200217/1001/mtrl/51/-/' . $image->ID . '/' . end($mainPhoto)
                ];
            }

            return $imagesArr;
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getRelevantItems($excludeIds = -1, $highPrice = -1, $webVisible = 1, $isRandom = 1, $isPopular = 0, $categoryId = -1)
    {
// Todo: replace categoryId with cart items relative categories
        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetRelevantItemsRequest>
    <Type>1056</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <CategoryID>$categoryId</CategoryID>
    <ItemID>-1</ItemID>
    <IncludeChildCategories>1</IncludeChildCategories>
    <IsRandom>$isRandom</IsRandom>
    <IsPopular>$isPopular</IsPopular>
    <LowPrice>0</LowPrice>
    <HighPrice>$highPrice</HighPrice>
    <ExcludeItemID>$excludeIds</ExcludeItemID>
    <WebVisible>$webVisible</WebVisible>
    <IsActive>1</IsActive>  
</ClientGetRelevantItemsRequest>
EOF;
        try {
            $result = $this->client->SendMessage(['Message' => $message]);
            $items = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            $itemsArr = $this->initializeProposalProducts($items->GetDataRows->GetRelevantItemsRow);

            return $itemsArr;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    private function initializeProposalProducts($products)
    {
        try {
            $prArr = [];
            $i = 0;
            foreach ($products as $pr) {
                if (strval($pr->WebVisible) !== "false" && strval($pr->Slug) !== '') {
                    $mainPhoto = (strval($pr->HasMainPhoto) !== 'false') ? explode('=', $pr->MainPhotoUrl) : [];
                    $prArr[] = [
                        'id' => $pr->ID,
                        'name' => $pr->Name2,
                        'slug' => $pr->Slug,
                        'summary' => $pr->SmallDescriptionHTML,
                        'body' => $pr->LargeDescriptionHTML,
                        'extraInfo' => $pr->InstructionsHTML,
                        'deliveryInfo' => $pr->DeliveryHTML,
                        'isVisible' => $pr->WebVisible,
                        'prCode' => $pr->Code,
                        'retailPrice' => $pr->RetailPrice,
                        'discount' => $pr->Discount,
                        'mainBarcode' => $pr->MainBarcode,
                        'webDiscount' => $pr->WebDiscountPerc,
                        'webPrice' => ($pr->Discount) ? round((floatval($pr->RetailPrice) * (100 - floatval($pr->Discount)) / 100), 2) : 0,
                        'outOfStock' => $pr->OutOfStock,
                        'volumeWeight' => $pr->VolumeWeight,
                        'manufacturerId' => $pr->ManufacturID,
                        'remainNotReserved' => $pr->RemainNotReserved,
                        'isNew' => $this->checkIfProductIsNew($pr->InsertDT),
                        'webFree' => $pr->WebFree,
                        'overAvailability' => $pr->OverAvailability,
                        'maxByOrder' => $pr->MaxByOrder,
                        'hasMainImage' => $pr->HasMainPhoto,
                        'categories' => $pr->AllCategoryIDs,
                        'imageUrl' => (empty($mainPhoto)) ? '' : 'images/products/FOSO/01102459200217/1001/mtrl/51/-/' . $pr->ID . '/' . end($mainPhoto)
//                        'imageUrl' => (strval($pr->HasMainPhoto) !== 'false') ? 'https://caron.cloudsystems.gr/FOeshopAPIWeb/DF.aspx?' . str_replace('[Serial]', '01102472475217', str_replace('&amp;', '&', $pr->MainPhotoUrl)) : ''
                    ];
                }
            }

            return $prArr;
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}