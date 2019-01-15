<?php


namespace App\Service;

use Psr\Log\LoggerInterface;
use App\Entity\Category;
use Doctrine\ORM\{
    EntityRepository, EntityManagerInterface
};
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class CategoryService
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var \ArrayObject
     */
    private $prCategories;

    /**
     * @var string
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
     * @var string
     */
    private $client;

    /**
     * CategoryService constructor.
     *
     * @param LoggerInterface $logger
     * @param EntityManagerInterface $em
     */
    public function __construct(LoggerInterface $logger, EntityManagerInterface $em, SessionInterface $session, $s1Credentials)
    {
        $this->logger = $logger;
        $this->em = $em;
        $this->session = $session;
        $this->authId = $session->get("authID");
        $this->kind = $s1Credentials['kind'];
        $this->domain = $s1Credentials['domain'];
        $this->appId = $s1Credentials['appId'];
        $this->companyId = $s1Credentials['companyId'];
        $this->client = new \SoapClient('https://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);
//        $encoders = array(new XmlEncoder());
//        $normalizers = array(new ObjectNormalizer());
//        $this->serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @param $ctg
     *
     * @return array
     */
    public function getCategoryInfo($ctg)
    {

        /*    $message = <<<EOF
    <?xml version="1.0" encoding="utf-16"?>
    <ClientGetCategoriesRequest>
        <Type>1007</Type>
        <Kind>$this->kind</Kind>
        <Domain>$this->domain</Domain>
        <AuthID>$this->authId</AuthID>
        <AppID>$this->appId</AppID>
        <CompanyID>$this->companyId</CompanyID>
        <IsTopLevel>-1</IsTopLevel>
        <IsVisible>1</IsVisible>
        <CategoryID>$ctg</CategoryID>
        <Slug>null</Slug>
    </ClientGetCategoriesRequest>
    EOF; */

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetFullCategoriesTreeRequest>
    <Type>1074</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <Level>-1</Level>
    <IsVisible>1</IsVisible>
    <CategoryID>$ctg</CategoryID>
    <Slug>null</Slug>
</ClientGetFullCategoriesTreeRequest>
EOF;


        try {
            $result = $this->client->SendMessage(['Message' => $message]);
            $resultXML = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            $category = $resultXML->GetDataRows->GetCategoriesRow;
            $categoryInfo = array(
                'id' => $category->CategoryID,
                'name' => $category->Name,
                'description' => $category->Description,
                'priority' => (int)$category->Priority,
                'children' => $category->ChildIDs,
                'isVisible' => $category->IsVisible,
                'slug' => $category->Slug,
                'hasMainImage' => $category->HasMainPhoto,
                'imageUrl' => ($category->HasMainPhoto) ? 'https://caron.cloudsystems.gr/FOeshopAPIWeb/DF.aspx?' . str_replace('[Serial]', '01102472475217', str_replace('&amp;', '&', $category->MainPhotoUrl)) : ''
            );
            dump($categoryInfo);

            return $categoryInfo;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * @param $authId
     * @return \ArrayObject
     * @throws \Exception
     */
    public function getCategoriesFromS1()
    {

        /*      $message = <<<EOF
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
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <Level>0</Level>
    <IsVisible>1</IsVisible>
    <CategoryID>-1</CategoryID>
    <Slug>null</Slug>
</ClientGetFullCategoriesTreeRequest>
EOF;

        try {
            $result = $this->client->SendMessage(['Message' => $message]);
            dump($message, $result);
            $resultXML = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            $categories = $this->initializeCategories($resultXML->GetDataRows->GetFullCategoriesTreeRow);

            return $categories;

        } catch (\SoapFault $sf) {
            throw $sf->faultstring;
        }
    }

    /**
     * @param $rootCategories
     * @param $authId
     * @return \ArrayObject
     * @throws \Exception
     */
    private function initializeCategories($rootCategories)
    {
        try {
//            return new Response(dump($rootCategories));
            $i = 0;
            foreach ($rootCategories as $category) {
//                if ((int)$category->IsVisible === 1) {
                $this->prCategories[] = array(
                    'id' => intval($category->CategoryID),
                    'name' => strval($category->CategoryName),
                    'description' => strval($category->Description),
                    'priority' => (int)$category->Priority,
                    'children' => '',
                    'isVisible' => strval($category->IsVisible),
                    'slug' => strval($category->Slug),
                    'itemsCount' => $this->getCategoryItemsCount($category->CategoryID),
                    'hasMainImage' => strval($category->HasMainPhoto),
                    'imageUrl' => (strval($category->HasMainPhoto) !== 'false') ? 'https://caron.cloudsystems.gr/FOeshopAPIWeb/DF.aspx?' . str_replace('[Serial]', '01102472475217', str_replace('&amp;', '&', $category->MainPhotoUrl)) : ''
                );
                $subCtgs = $this->getSubCategories($category->CategoryID);
                dump($subCtgs);
                if ($subCtgs) {
                    $childArr = array();
                    foreach ($subCtgs->GetDataRows->GetFullCategoriesTreeRow as $category) {
                        $childArr[] = array(
                            'id' => intval($category->CategoryID),
                            'name' => strval($category->CategoryName),
                            'description' => strval($category->Description),
                            'priority' => (int)$category->Priority,
                            'isVisible' => strval($category->IsVisible),
                            'slug' => strval($category->Slug),
                            'itemsCount' => $this->getCategoryItemsCount($category->CategoryID),
                            'hasMainImage' => strval($category->HasMainPhoto),
                            'imageUrl' => (strval($category->HasMainPhoto) !== 'false') ? 'https://caron.cloudsystems.gr/FOeshopAPIWeb/DF.aspx?' . str_replace('[Serial]', '01102472475217', str_replace('&amp;', '&', $category->MainPhotoUrl)) : ''
                        );
                        array_multisort(array_column($childArr, "priority"), $childArr);
                        $this->prCategories[$i]['children'] = $childArr;
                    }
                }
                $i++;
            }
            return $this->prCategories;
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * @param $authId
     *
     * @return mixed
     * @throws \Exception
     */
    public function getCategories($authId)
    {
        try {
//            $rootCategories = $this->em->getRepository(Category::class)->findAll();
//            if ($rootCategories) {
//                return $rootCategories;
//            }else{
//                $rootCategories = $this->getCategoriesFromS1($this->session->get("authID"));
//            }
            $rootCategories = $this->getCategoriesFromS1($this->authId);
            return $rootCategories;
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getSubCategories($ctgId)
    {

        /*    $message = <<<EOF
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
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <Level>1</Level>
    <IsVisible>1</IsVisible>
    <CategoryID>$ctgId</CategoryID>
    <Slug>null</Slug>
</ClientGetFullCategoriesTreeRequest>
EOF;

        try {
            $result = $this->client->SendMessage(['Message' => $message]);
            dump($message, $result);
//            return $result->SendMessageResult;
//            return str_replace("utf-16", "utf-8", $result->SendMessageResult);
            $resultXML = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
//            $subCategories = $this->initializeSubCategories($resultXML->GetDataRows->GetFullCategoriesTreeRow);

            return $resultXML;
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    private function initializeSubCategories($subCategories)
    {
        $subCategoriesArr = [];
        foreach ($subCategories as $category) {
//                if ((int)$category->IsVisible === 1) {
            $subCategoriesArr[] = array(
                'id' => intval($category->CategoryID),
                'name' => strval($category->CategoryName),
                'description' => strval($category->Description),
                'priority' => (int)$category->Priority,
                'children' => '',
                'isVisible' => strval($category->IsVisible),
                'slug' => strval($category->Slug),
                'itemsCount' => $this->getCategoryItemsCount($category->CategoryID),
                'hasMainImage' => strval($category->HasMainPhoto),
                'imageUrl' => (strval($category->HasMainPhoto) !== 'false') ? 'https://caron.cloudsystems.gr/FOeshopAPIWeb/DF.aspx?' . str_replace('[Serial]', '01102472475217', str_replace('&amp;', '&', $category->MainPhotoUrl)) : ''
            );
        }
        return $subCategoriesArr;
    }

    public function getCategoryItemsCount($ctgId)
    {

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetCategoryItemsCountRequest>
    <Type>1011</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <pagesize>10</pagesize>
    <pagenumber>0</pagenumber>
    <CategoryID>$ctgId</CategoryID>
    <SearchToken>null</SearchToken>
    <IncludeChildCategories>1</IncludeChildCategories>
    <MakeID>null</MakeID>
    <ManufacturID>null</ManufacturID>
    <LowPrice>-1</LowPrice>
    <HighPrice>-1</HighPrice>
</ClientGetCategoryItemsCountRequest>
EOF;
        try {
            $result = $this->client->SendMessage(['Message' => $message]);
            dump($message, $result);
//            return $result->SendMessageResult;
//            return str_replace("utf-16", "utf-8", $result->SendMessageResult);
            return simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    public function getChildrenCategories($ctgId)
    {
        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetCategoriesRequest>
    <Type>1007</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <IsTopLevel>1</IsTopLevel>
    <IsVisible>-1</IsVisible>
    <CategoryID>$ctgId</CategoryID>
    <Slug>null</Slug>
</ClientGetCategoriesRequest>
EOF;
        try {
            $result = $this->client->SendMessage(['Message' => $message]);
            dump($message, $result);
            $resultXML = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
//            $categories = $this->initializeCategories($resultXML->GetDataRows->GetCategoriesRow, $authId);

            return strval($resultXML->GetDataRows->GetCategoriesRow->ChildIDs);

        } catch (\SoapFault $sf) {
            throw $sf->faultstring;
        }
    }
}