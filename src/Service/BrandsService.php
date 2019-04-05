<?php

namespace App\Service;


use Symfony\Component\HttpFoundation\Session\SessionInterface;

class BrandsService
{
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
    private $client;

    /**
     * @var string
     */
    private $companyId;

    public function __construct(SessionInterface $session, $s1Credentials)
    {
        $this->authId = $session->get("authID");
        $this->kind = $s1Credentials['kind'];
        $this->domain = $s1Credentials['domain'];
        $this->appId = $s1Credentials['appId'];
        $this->companyId = $s1Credentials['companyId'];
        $this->client = new \SoapClient('https://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);
    }

    /**
     * @param $slug
     *
     * @return \SimpleXMLElement
     * @throws \Exception
     */
    public function getBrands($slug)
    {

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetMakeRequest>
    <Type>1032</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <pagesize>1000</pagesize>
    <pagenumber>0</pagenumber>
    <MakeID>-1</MakeID>
    <Slug>$slug</Slug>
</ClientGetMakeRequest>
EOF;
        try {
            $result = $this->client->SendMessage(['Message' => $message]);
            $resultXML = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            $brands = $this->initializeBrands($resultXML->GetDataRows->GetMakeRow);
            return $brands;

        } catch (\SoapFault $sf) {
            throw $sf->faultstring;
        }
    }

    /**
     * @param $brands
     * @return array
     * @throws \Exception
     */
    private function initializeBrands($brands)
    {
        try {
            $brandsArr = [];
            foreach ($brands as $brand) {
//                if ($brand->) Todo: get only IsActive when ready by Sotiris
                $brandsArr[] = array(
                    'id' => $brand->ID,
                    'name' => $brand->Name,
                    'slug' => $brand->Slug,
                    'hasMainImage' => $brand->HasMainPhoto,
//                    'imageUrl' => ($brand->HasMainPhoto) ? 'https://caron.cloudsystems.gr/FOeshopAPIWeb/DF.aspx?' . str_replace('[Serial]', '01102472475217', str_replace('&amp;', '&', $brand->MainPhotoUrl)) : ''
                );
//            }
            }

            return $brandsArr;
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getManufacturers($slug)
    {
        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetManufactorRequest>
    <Type>1066</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <pagesize>20</pagesize>
    <pagenumber>0</pagenumber>
    <ManufactorID>-1</ManufactorID>
    <Slug>$slug</Slug>
</ClientGetManufactorRequest>
EOF;
        try {
            $result = $this->client->SendMessage(['Message' => $message]);
            $resultXML = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            $brands = $this->initializeBrands($resultXML->GetDataRows->GetManufactorRow);
            return $brands;

        } catch (\SoapFault $sf) {
            throw $sf->faultstring;
        }
    }

    public function getCategoryManufacturers($ctgId)
    {
        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetCategoryManufacturRequest>
    <Type>1070</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <CategoryID>$ctgId</CategoryID>
    <IncludeChildCategories>1</IncludeChildCategories>
</ClientGetCategoryManufacturRequest>
EOF;
        try {
            $result = $this->client->SendMessage(['Message' => $message]);
            dump($message, $result);
            $resultXML = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            $brands = $this->initializeManufacturers($resultXML->GetDataRows->GetCategoryManufacturRow);

            return $brands;
        } catch (\SoapFault $sf) {
            throw $sf->faultstring;
        }
    }

    /**
     * @param $manufacturers
     * @return array
     * @throws \Exception
     */
    private function initializeManufacturers($manufacturers)
    {
        try {
            $manufacturersArr = [];
            foreach ($manufacturers as $manufacturer) {
//                if ($brand->) Todo: get only IsActive when ready by Sotiris
                $manufacturersArr[] = array(
                    'id' => $manufacturer->ManufacturID,
                    'name' => $manufacturer->ManufacturName,
//                    'slug' => $brand->Slug,
//                    'hasMainImage' => $brand->HasMainPhoto,
//                    'imageUrl' => ($brand->HasMainPhoto) ? 'https://caron.cloudsystems.gr/FOeshopAPIWeb/DF.aspx?' . str_replace('[Serial]', '01102472475217', str_replace('&amp;', '&', $brand->MainPhotoUrl)) : ''
                );
//            }
            }

            return $manufacturersArr;
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

}
