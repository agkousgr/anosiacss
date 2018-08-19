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
        $this->authId = $session->get("authID");
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
        $client = new \SoapClient('https://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetCategoriesRequest>
    <Type>1007</Type>
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
    <IsTopLevel>-1</IsTopLevel>
    <IsVisible>1</IsVisible>
    <CategoryID>$ctg</CategoryID>
    <Slug>null</Slug>
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
     * @return \ArrayObject
     * @throws \Exception
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

    /**
     * @param $rootCategories
     * @param $authId
     * @return \ArrayObject
     * @throws \Exception
     */
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
            $rootCategories = $this->getCategoriesFromS1($authId);
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
    <Slug>null</Slug>
</ClientGetCategoryChildrenRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
//            dump($message, $result);
//            return $result->SendMessageResult;
//            return str_replace("utf-16", "utf-8", $result->SendMessageResult);
            return simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

}