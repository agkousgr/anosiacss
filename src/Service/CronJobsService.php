<?php

namespace App\Service;

use App\Entity\{Category, CategoryTopSeller, Country, Parameters};
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
    }

    /**
     * @param $authId
     *
     * @throws \SoapFault
     */
    private function getCategoriesFromS1($authId): void
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
    <Level>-1</Level>
    <IsVisible>-1</IsVisible>
    <CategoryID>-1</CategoryID>
    <Slug>null</Slug>
</ClientGetFullCategoriesTreeRequest>
EOF;

        try {
            $result = $this->client->SendMessage(['Message' => $message]);
            $resultXML = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            $sortable = [];
            foreach ($resultXML->GetDataRows->GetFullCategoriesTreeRow as $row) {
                $sortable[] = $row;
            }
            uasort($sortable, function ($a, $b) {
                return intval($a->CategoryID) <=> intval($b->CategoryID);
            });

            // Save plain categories
            foreach ($sortable as $item) {
                $category = new Category();
                $category->setS1id(intval($item->CategoryID))
                    ->setS1Level(intval($item->Level))
                    ->setName($item->CategoryName)
                    ->setSlug($item->Slug)
                    ->setIsVisible(boolval($item->IsVisible))
                    ->setPriority(intval($item->Priority))
                    ->setImageUrl((strval($item->HasMainPhoto) !== 'false') ?
                        'https://caron.cloudsystems.gr/FOeshopAPIWeb/DF.aspx?' .
                        str_replace('[Serial]', '01102472475217', str_replace('&amp;', '&', $item->MainPhotoUrl)) :
                        null);
                $this->em->persist($category);
            }
            $this->em->flush();

            // Save children of each category, if any
            foreach ($sortable as $item) {
                if (property_exists($item, 'ParentIDs')) {
                    $cat = $this->em->getRepository(Category::class)->findOneBy(['s1id' => $item->CategoryID]);
                    $parents = explode(',', $item->ParentIDs);
                    foreach ($parents as $parent) {
                        $parentCategory = $this->em->getRepository(Category::class)->findOneBy(['s1id' => $parent]);
                        if (null !== $parentCategory)
                            $cat->addParent($parentCategory);
                    }
                }
            }
            $this->em->flush();

            return;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
            throw $sf;
        }
    }

    public function syncCategoryTopSellers(): void
    {
        foreach ($this->em->getRepository(Category::class)->findBy(['s1Level' => 0]) as $category) {
            $categoryId = $category->getS1id();
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
    <IsRandom>0</IsRandom>
    <IsPopular>1</IsPopular>
    <LowPrice>-1</LowPrice>
    <HighPrice>-1</HighPrice>
    <ExcludeItemID>null</ExcludeItemID>
    <WebVisible>1</WebVisible>
    <IsActive>1</IsActive>  
</ClientGetRelevantItemsRequest>
EOF;
            try {
                $result = $this->client->SendMessage(['Message' => $message]);
                $items = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
                if (intval($items->RowsCount > 0)) {
                    $oldEntries = $this->em->getRepository(CategoryTopSeller::class)->findByCategory($category);
                    foreach ($oldEntries as $oldEntry) {
                        $this->em->remove($oldEntry);
                    }
                    $this->em->flush();
                    $internalCounter = 0;
                    foreach ($items->GetDataRows->GetRelevantItemsRow as $item) {
                        if ($internalCounter < 5) {
                            $mainPhoto = (strval($item->MainPhotoUrl) !== 'false') ? explode('=', $item->MainPhotoUrl) : [];
                            $topSeller = new CategoryTopSeller();
                            $topSeller->setSoftOneId(intval($item->ID))
                                ->setName($item->Name)
                                ->setSlug($item->Slug)
                                ->setCategory($category)
                                ->setImageUrl(
                                    $item->HasMainPhoto ?
                                        'FOSO/01102459200217/1001/mtrl/51/-/' . end($mainPhoto) :
                                        null
                                );
                            $this->em->persist($topSeller);
                            $internalCounter++;
                        }
                    }
                }
            } catch (\SoapFault $sf) {
                echo $sf->faultstring;
            }
        }
        $this->em->flush();
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

            if ((int)$items->RowsCount > 0) {
                return $items->GetDataRows->GetAvailabilityTypeRow;
            }
            return [];
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }

    }

    /**
     * Synchronize parameters with S1
     */
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
                foreach ($items->GetDataRows->children() as $item) {
                    $arr = get_object_vars($item);
                    foreach ($arr as $key => $value) {
                        $param = $this->em->getRepository(Parameters::class)->findOneBy(['name' => $key]);
                        if ($param) {
                            $param->setS1Value($value);
                            $this->em->flush();
                        } else {
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


    /**
     * Synchronize countries with S1
     */
    public function getCountries()
    {

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetCountryRequest>
    <Type>1075</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
</ClientGetCountryRequest>
EOF;

        try {
            $result = $this->client->SendMessage(['Message' => $message]);
            $items = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));

            if ((int)$items->RowsCount > 0) {
                foreach ($items->GetDataRows->GetCountryRow as $value) {
                    $country = $this->em->getRepository(Country::class)->findOneBy(['s1Id' => $value['CountryID']]);
                    if ($country) {
                        $country->setIntCode($value->InternationalCode);
                        $country->setName($value->Name);
                        $this->em->flush();
                    } else {
                        $country = new Country();
                        $country->setName($value->Name);
                        $country->setS1Id(intval($value->CountryID));
                        $country->setIntCode($value->InternationalCode);
                        $this->em->persist($country);
                        $this->em->flush();
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