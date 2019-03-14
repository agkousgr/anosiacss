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
//            return true;
            return ((string)$response->IsValid === 'true') ? true : false;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    public function updateProducts($id, $oldId, $slug, $oldSlug, $seoTitle, $seoDescription, $seoKeywords, $ingredients, $instructions, $smallDescription, $largeDescription, $manufacturerId, $categoryIds = '')
    {

//        $s1ProductData = $this->productService->getItems($id, $keyword = 'null', 1, $sortBy = 'null', $isSkroutz = -1, $makeId = 'null', $priceRange = 'null');

        $smallDescription = htmlspecialchars('<body>' . $smallDescription . '</body>');
        $largeDescription = htmlspecialchars('<body>' . $largeDescription . '</body>');
        $ingredients = htmlspecialchars('<body>' . $ingredients . '</body>');
        $instructions = htmlspecialchars('<body>' . $instructions . '</body>');

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
    <OldItemID>$oldId</OldItemID>
    <Slug>$slug</Slug>
    <SEOTitle>$seoTitle</SEOTitle>
    <SEODescription>$seoDescription</SEODescription>
    <SEOKeywords>$seoKeywords</SEOKeywords>
    <OldSlug>$oldSlug</OldSlug>
    <Ingredients>$ingredients</Ingredients>
    <Instructions>$instructions</Instructions>
    <MakeID></MakeID>
    <CategoryIDs>$categoryIds</CategoryIDs>
    <ManufacturID>$manufacturerId</ManufacturID>
    <SmallDescription>$smallDescription</SmallDescription>
    <LargeDescription>$largeDescription</LargeDescription>
    <WebVisible></WebVisible>
    <OutOfStock></OutOfStock>
    <Name2></Name2>
</ClientSetItemSEORequest>
EOF;
        try {
//            dump($message);
//            die();
            $result = $this->client->SendMessage(['Message' => $message]);
//            $items = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
//            dump($message, $result);

            return;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }


}