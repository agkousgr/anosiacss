<?php


namespace App\Service;


use App\Entity\Parameters;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CronJobsService
{
    /**
     * @var \App\Service\SoftoneLogin
     */
    protected $softoneLogin;

    /**
     * @var string
     */
    private $authId;

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

    public function __construct(EntityManagerInterface $em, SoftoneLogin $softoneLogin, SessionInterface $session, $s1Credentials)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $this->softoneLogin = $softoneLogin;
        $this->em = $em;
        $this->authId = $session->get("authID");
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
            dump($message, $resultXML);
//            die();
            $this->initializeCategories($resultXML->GetDataRows->GetFullCategoriesTreeRow, $authId, 0);

            return;

        } catch (\SoapFault $sf) {
            throw $sf->faultstring;
        }
    }

    private function initializeCategories($categories, $authId, $parentId = 0)
    {

        foreach ($categories as $category) {
            dump($category);
            $this->counter++;
//            if ($this->counter < 150) {
//                if ((int)$category->IsVisible === 1) {
            $subCtgs = $this->getSubCategories((int)$category->CategoryID, $authId, $category->ID, 1);
            if ($this->counter === 2) {
                dump($this->prCategories);
                die();
            }
//            continue;
            $this->prCategories[] = array(
                'id' => (int)$category->CategoryID,
                'name' => (string)$category->CategoryName,
                'description' => (string)$category->CategoryInternalName,
                'priority' => (int)$category->Priority,
                'isVisible' => (int)$category->IsVisible,
                'parentId' => (int)$parentId,
                'slug' => (string)$category->Slug,
                'children' => ($subCtgs) ? $this->initializeCategories($subCtgs, $authId, (int)$category->CategoryID) : '',
//                'itemsCount' => $this->getCategoryItemsCount($category->ID),
                'hasMainImage' => (string)$category->HasMainPhoto,
                'imageUrl' => (strval($category->HasMainPhoto) !== 'false') ? 'https://caron.cloudsystems.gr/FOeshopAPIWeb/DF.aspx?' . str_replace('[Serial]', '01102472475217', str_replace('&amp;', '&', $category->MainPhotoUrl)) : ''
            );
//            foreach ($subCtgs as $subCtg) {
//                $this->initializeCategories()
//            }
//            if ($category->ChildIDs) {
//                $subCtg = $this->getSubCategories($category->ChildIDs, $authId, $category->ID, 0);
//            }
//            }
        }
        return;

    }

    public function getSubCategories($ctgId, $authId, $parentId, $level)
    {

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetFullCategoriesTreeRequest>
    <Type>1074</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <Level>1</Level>
    <IsVisible>-1</IsVisible>
    <CategoryID>$ctgId</CategoryID>
    <Slug>null</Slug>
</ClientGetFullCategoriesTreeRequest>
EOF;

        try {
            $result = $this->client->SendMessage(['Message' => $message]);
            $resultXML = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            $this->counter++;
            dump($message, $result);
            return $resultXML->GetDataRows->GetFullCategoriesTreeRow;
            if ($items->GetDataRows->GetFullCategoriesTreeRow !== false) {
//                    $this->tmpArr = [];
                return $this->initializeCategories($items->GetDataRows->GetFullCategoriesTreeRow, $authId, $parentId);
            }


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

    public function getParams()
    {

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetParamsRequest>
    <Type>1064</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
</ClientGetParamsRequest>
EOF;

        try {
            $result = $this->client->SendMessage(['Message' => $message]);
            $items = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
//            dump($message, $result);
            if ((int)$items->RowsCount > 0) {
                foreach($items->GetDataRows->children() as $item){
                    $arr = get_object_vars($item);
                    foreach($arr as $key=>$value){
                        $param = $this->em->getRepository(Parameters::class)->findOneBy(['name' => $key]);
                        if ($param) {
                            $param->setS1Value($value);
                            $this->em->flush();
                        }else{
                            $param = new Parameters();
                            $param->setName($key);
                            $param->setS1Value($value);
                            $this->em->persist($param);
                            $this->em->flush();
                        }
                    }
                }
                return;
            }
            return;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }

    }

}