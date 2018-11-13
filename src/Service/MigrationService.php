<?php


namespace App\Service;


use Symfony\Component\HttpFoundation\Session\SessionInterface;

class MigrationService
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
    private $companyId;

    /**
     * @var \SoapClient
     */
    private $client;

    /**
     * @var ProductService
     */
    private $productService;

    public function __construct(SessionInterface $session, ProductService $productService, $s1Credentials)
    {
        $this->authId = $session->get("authID");
        $this->kind = $s1Credentials['kind'];
        $this->domain = $s1Credentials['domain'];
        $this->appId = $s1Credentials['appId'];
        $this->companyId = $s1Credentials['companyId'];
        $this->client = new \SoapClient('https://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);
        $this->productService = $productService;
    }

    public function saveImage($imageName, $isMain, $id)
    {

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientSetCCCSOPHDiskFileRequest>
    <Type>1058</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <sodtype>51</sodtype>
    <sosource>null</sosource>
    <TableName>mtrl</TableName>
    <TableID>$id</TableID>
    <FileName>$imageName</FileName>
    <IsPhoto>true</IsPhoto>
    <IsDoc>false</IsDoc>
    <IsMain>$isMain</IsMain>
</ClientSetCCCSOPHDiskFileRequest>
EOF;
        try {
            $result = $this->client->SendMessage(['Message' => $message]);
            $response = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            dump($message, $result);
            return ((string)$response->IsValid === 'true') ? true : false;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }


    public function updateProducts($id, $slug, $oldSlug, $seoTitle, $seoDescription, $seoKeywords, $ingredients, $instructions, $smallDescription, $largeDescription)
    {

        $s1ProductData = $this->productService->getItems($id, $keyword = 'null', 1, $sortBy = 'null', $isSkroutz = -1, $makeId = 'null', $priceRange = 'null');

        dump($s1ProductData);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientSetItemSEORequest>
    <Type>1060</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <ItemID>$id</ItemID>
    <Slug>$slug</Slug>
    <SEOTitle>$seoTitle</SEOTitle>
    <SEODescription>$seoDescription</SEODescription>
    <SEOKeywords>$seoKeywords</SEOKeywords>
    <OldSlug>$oldSlug</OldSlug>
    <Ingredients>$ingredients</Ingredients>
    <Instructions>$instructions</Instructions>
    <MakeID></MakeID>
    <CategoryIDs></CategoryIDs>
    <ManufacturID>1001</ManufacturID>
    <SmallDescription>$smallDescription</SmallDescription>
    <LargeDescription>$largeDescription</LargeDescription>
</ClientSetItemSEORequest>
EOF;
        try {
//            $itemsArr = array();
//            $result = $this->client->SendMessage(['Message' => $message]);
//            $items = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
//            dump($message, $result);

            return;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }


}