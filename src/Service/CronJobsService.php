<?php


namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;

class CronJobsService
{

    /**
     * @var int
     */
    private $counter;

    /**
     * @var \ArrayObject
     */
    private $prCategories;

    /**
     * @var EntityManagerInterface
     */
    private $em;

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
     * @var ProductService
     */
    private $productService;

    public function __construct(EntityManagerInterface $em, $s1Credentials)
    {
        $this->em = $em;
        $this->kind = $s1Credentials['kind'];
        $this->domain = $s1Credentials['domain'];
        $this->appId = $s1Credentials['appId'];
        $this->companyId = $s1Credentials['companyId'];
        $this->counter = 0;
        $this->client = new \SoapClient('https://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);
    }

    public function synchronizeCategories($authId)
    {
        $this->getCategoriesFromS1($authId);
        return $this->prCategories;
    }

    /**
     * @param $authId
     * @return \ArrayObject
     * @throws \Exception
     */
    private function getCategoriesFromS1($authId)
    {
// Todo: Probably all the synchronization can be using this function instead of getSubCategories
        /*        $message = <<<EOF
        <?xml version="1.0" encoding="utf-16"?>
        <ClientGetCategoriesRequest>
            <Type>1007</Type>
            <Kind>$this->kind</Kind>
            <Domain>$this->domain</Domain>
            <AuthID>$authId</AuthID>
            <AppID>$this->appId</AppID>
            <CompanyID>$this->companyId</CompanyID>
            <IsTopLevel>1</IsTopLevel>
            <IsVisible>-1</IsVisible>
            <CategoryID>-1</CategoryID>
            <Slug>null</Slug>
        </ClientGetCategoriesRequest>
        EOF; */

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetFullCategoriesTreeRequest>
    <Type>1074</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <Level>0</Level>
    <IsVisible>-1</IsVisible>
    <CategoryID>-1</CategoryID>
    <Slug>null</Slug>
</ClientGetFullCategoriesTreeRequest>
EOF;

        try {
            $result = $this->client->SendMessage(['Message' => $message]);
            $resultXML = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            dump($message, $result);
//            die();
            $this->initializeCategories($resultXML->GetDataRows->GetFullCategoriesTreeRow, $authId, 0);

            return;

        } catch (\SoapFault $sf) {
            throw $sf->faultstring;
        }
    }

    private function initializeCategories($categories, $authId, $parentId)
    {
        foreach ($categories as $category) {
            $this->counter++;
//            if ($this->counter < 150) {
//                if ((int)$category->IsVisible === 1) {
            $this->prCategories[] = array(
                'id' => $category->CategoryID,
                'name' => $category->Name,
                'description' => $category->Description,
                'priority' => (int)$category->Priority,
                'isVisible' => $category->IsVisible,
                'parentId' => $category->ParentID,
                'slug' => $category->Slug,
//                'itemsCount' => $this->getCategoryItemsCount($category->ID),
                'hasMainImage' => $category->HasMainPhoto,
                'imageUrl' => (strval($category->HasMainPhoto) !== 'false') ? 'https://caron.cloudsystems.gr/FOeshopAPIWeb/DF.aspx?' . str_replace('[Serial]', '01102472475217', str_replace('&amp;', '&', $category->MainPhotoUrl)) : ''
            );
            $this->getSubCategories($category->CategoryID, $authId, $category->ID, 1);
//            }
        }
        return;

    }

    public function getSubCategories($ctgId, $authId, $parentId, $level)
    {

        /*      $message = <<<EOF
      <?xml version="1.0" encoding="utf-16"?>
      <ClientGetCategoryChildrenRequest>
          <Type>1009</Type>
          <Kind>$this->kind</Kind>
          <Domain>$this->domain</Domain>
          <AuthID>$authId</AuthID>
          <AppID>$this->appId</AppID>
          <CompanyID>$this->companyId</CompanyID>
          <CategoryID>$ctgId</CategoryID>
          <Slug>null</Slug>
      </ClientGetCategoryChildrenRequest>
      EOF; */

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetFullCategoriesTreeRequest>
    <Type>1074</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <Level>$level</Level>
    <IsVisible>-1</IsVisible>
    <CategoryID>$ctgId</CategoryID>
    <Slug>null</Slug>
</ClientGetFullCategoriesTreeRequest>
EOF;

        try {
            $result = $this->client->SendMessage(['Message' => $message]);
            $items = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            $this->counter++;
            dump($message, $result);
            if ($items !== false) {
//                    $this->tmpArr = [];
                return $this->initializeCategories($items->GetDataRows->GetCategoryChildrenRow, $authId, $ctgId);
            }
//            return $result->SendMessageResult;
//            return str_replace("utf-16", "utf-8", $result->SendMessageResult);
            return;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    public function getAvailabilityTypes($typeId = -1, $authId)
    {

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetAvailabilityTypeRequest>
    <Type>1068</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <AvailabilityTypeID>-1</AvailabilityTypeID>
</ClientGetAvailabilityTypeRequest>
EOF;

        try {
            $result = $this->client->SendMessage(['Message' => $message]);
            $items = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            dump($message, $result, $items->GetDataRows->GetAvailabilityTypeRow);
            if ((int)$items->RowsCount > 0) {
                return $items->GetDataRows->GetAvailabilityTypeRow;
            }
            return [];
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }

    }
}