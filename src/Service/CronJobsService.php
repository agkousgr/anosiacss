<?php


namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;

class CronJobsService
{
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

    public function __construct(EntityManagerInterface $em, $s1Credentials)
    {
        $this->em = $em;
        $this->kind = $s1Credentials['kind'];
        $this->domain = $s1Credentials['domain'];
        $this->appId = $s1Credentials['appId'];
        $this->companyId = $s1Credentials['companyId'];
    }

    public function synchronizeCategories($authId)
    {
        $this->getCategoriesFromS1($authId);
        dump($this->prCategories);
    }

    /**
     * @param $authId
     * @return \ArrayObject
     * @throws \Exception
     */
    private function getCategoriesFromS1($authId)
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
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
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $resultXML = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            $categories = $this->initializeCategories($resultXML->GetDataRows->GetCategoriesRow, $authId);

            return $categories;

        } catch (\SoapFault $sf) {
            throw $sf->faultstring;
        }
    }

    private function initializeCategories($categories, $authId)
    {

        foreach ($categories as $category) {
//                if ((int)$category->IsVisible === 1) {
            $this->prCategories[] = array(
                'id' => $category->ID,
                'name' => $category->Name,
                'description' => $category->Description,
                'priority' => (int)$category->Priority,
                'children' => $this->getSubCategories($category->ID, $authId),
                'isVisible' => $category->IsVisible,
                'slug' => $category->Slug,
//                'itemsCount' => $this->getCategoryItemsCount($category->ID),
                'hasMainImage' => $category->HasMainPhoto,
                'imageUrl' => ($category->HasMainPhoto) ? 'https://caron.cloudsystems.gr/FOeshopAPIWeb/DF.aspx?' . str_replace('[Serial]', '01102459200617', str_replace('&amp;', '&', $category->MainPhotoUrl)) : ''
            );
        }
        return;

    }

    public function getSubCategories($ctgId, $authId)
    {

        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
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
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $items = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
//            dump($message, $result);
//            return $result->SendMessageResult;
//            return str_replace("utf-16", "utf-8", $result->SendMessageResult);
            return;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }
}