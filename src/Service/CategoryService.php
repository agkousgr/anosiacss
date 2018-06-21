<?php


namespace App\Service;

use DOMDocument;
use Monolog\Logger;
use phpDocumentor\Reflection\Types\Array_;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
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
     * CategoryService constructor.
     *
     * @param LoggerInterface $logger
     * @param EntityManagerInterface $em
     */
    public function __construct(LoggerInterface $logger, EntityManagerInterface $em, SessionInterface $session)
    {
        $this->logger = $logger;
        $this->em = $em;
        $this->session = $session;
        $encoders = array(new XmlEncoder());
        $normalizers = array(new ObjectNormalizer());
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @param $authId
     * @param $ctg
     *
     * @return object
     */
    public function getCategoryInfo($ctg, $authId)
    {
        $client = new \SoapClient('https://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetCategoriesRequest>
    <Type>1007</Type>
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <AuthID>$authId</AuthID>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
    <IsTopLevel>-1</IsTopLevel>
    <IsVisible>1</IsVisible>
    <CategoryID>$ctg</CategoryID>
    <Slug></Slug>
</ClientGetCategoriesRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $resultXML = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            $category = $resultXML->GetDataRows->GetCategoriesRow;
            $categoryInfo = array(
                'id' => $category->ID,
                'name' => $category->Name,
                'description' => $category->Description,
                'priority' => (int)$category->Priority,
                'children' => $category->ChildIDs,
                'isVisible' => $category->IsVisible,
                'slug' => $category->Slug,
                'hasMainImage' => $category->HasMainPhoto,
                'imageUrl' => ($category->HasMainPhoto) ? 'https://caron.cloudsystems.gr/FOeshopAPIWeb/DF.aspx?' . str_replace('[Serial]', '01102459200617', str_replace('&amp;', '&', $category->MainPhotoUrl)) : ''
            );
            dump($categoryInfo);

            return $categoryInfo;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * @param $authId
     *
     * @return object
     */
    public function getCategoriesFromS1($authId)
    {
        $client = new \SoapClient('https://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetCategoriesRequest>
    <Type>1007</Type>
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <AuthID>$authId</AuthID>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
    <IsTopLevel>1</IsTopLevel>
    <IsVisible>-1</IsVisible>
    <CategoryID>-1</CategoryID>
</ClientGetCategoriesRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $resultXML = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
//            dump($resultXML);
            $categories = $this->initializeCategories($resultXML->GetDataRows->GetCategoriesRow, $authId);

            return $categories;

            // -------------------------------------------------

            return $resultXML;

            $doc = new DOMDocument();
            $doc->formatOutput = TRUE;
            $doc->loadXML($resultXML->GetDataRows->GetCategoriesRow->asXML());
            $xml = $doc->saveXML();

//            return new Response(var_dump($xml));

//            $categoriesXML = <<<EOF
//    $xml
//EOF;

            $categories = $this->serializer->deserialize($xml, Category::class, 'xml');
//            return new Response(var_dump($categories));
//            return str_replace("utf-16", "utf-8", $result->SendMessageResult);
            return $categories;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    private function initializeCategories($rootCategories, $authId)
    {
        try {
//            return new Response(dump($rootCategories));
            $i = 0;
            foreach ($rootCategories as $category) {
//                if ((int)$category->IsVisible === 1) {
                    $this->prCategories[] = array(
                        'id' => $category->ID,
                        'name' => $category->Name,
                        'description' => $category->Description,
                        'priority' => (int)$category->Priority,
                        'children' => '',
                        'isVisible' => $category->IsVisible,
                        'slug' => $category->Slug,
                        'hasMainImage' => $category->HasMainPhoto,
                        'imageUrl' => ($category->HasMainPhoto) ? 'https://caron.cloudsystems.gr/FOeshopAPIWeb/DF.aspx?' . str_replace('[Serial]', '01102459200617', str_replace('&amp;', '&', $category->MainPhotoUrl)) : ''
                    );
                    $subCtgs = $this->getSubCategories($category->ID, $authId);

                    $childArr = array();
                    foreach ($subCtgs->GetDataRows->GetCategoryChildrenRow as $child) {
                        $childArr[] = array(
                            'id' => $child->ID,
                            'name' => $child->Name,
                            'description' => $child->Description,
                            'priority' => (int)$child->Priority,
                            'isVisible' => $child->IsVisible,
                            'slug' => $child->Slug,
                            'hasMainImage' => $child->HasMainPhoto,
                            'imageUrl' => ($child->HasMainPhoto) ? 'https://caron.cloudsystems.gr/FOeshopAPIWeb/DF.aspx?' . str_replace('[Serial]', '01102459200617', str_replace('&amp;', '&', $child->MainPhotoUrl)) : ''
                        );
                        array_multisort(array_column($childArr, "priority"), $childArr);
                        $this->prCategories[$i]['children'] = $childArr;
                    }
//                }

//                dump($childArr);

//                $children = ($category->ChildIDs) ? explode(',', $category->ChildIDs) : '';
//                if ($children) {
//                    $childArr = array();
//                    foreach ($children as $child) {
//
//                        $childArr[] = array(
//                            'id' => $child
//                        );
//                    }
//                    $this->prCategories[$i]['children'] = $childArr;
//                }
//                dump($this->prCategories);
                $i++;
            }
//            return new Response(dump(print_r($this->prCategories)));
            return $this->prCategories;
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getCategories()
    {
        try {
//            $rootCategories = $this->em->getRepository(Category::class)->findAll();
//            if ($rootCategories) {
//                return $rootCategories;
//            }else{
//                $rootCategories = $this->getCategoriesFromS1($this->session->get("authID"));
//            }
            $rootCategories = $this->getCategoriesFromS1($this->session->get("authID"));
            return $rootCategories;
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getSubCategories($ctgId, $authId)
    {
        $client = new \SoapClient('https://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetCategoryChildrenRequest>
<Type>1009</Type>
<Kind>1</Kind>
<Domain>pharmacyone</Domain>
<AuthID>$authId</AuthID>
<AppID>157</AppID>
<CompanyID>1000</CompanyID>
<CategoryID>$ctgId</CategoryID>
</ClientGetCategoryChildrenRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
//            return $result->SendMessageResult;
//            return str_replace("utf-16", "utf-8", $result->SendMessageResult);
            return simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    private function createCategory(Category $category, EntityManagerInterface $em, LoggerInterface $logger)
    {

    }

    private function updateCategory(Category $category, EntityManagerInterface $em, LoggerInterface $logger)
    {

    }
}