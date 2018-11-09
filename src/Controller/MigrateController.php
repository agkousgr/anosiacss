<?php


namespace App\Controller;


class MigrateController extends MainController
{
    public function updateS1()
    {
        $client = new \SoapClient('https://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $authId = $this->session->get("authID");

        $body = '<body><h1>Ingredients 23</h1></body>';
        $body = '&lt;Body&gt;&lt;h1&gt;Ingredients&lt;/h1&gt;&lt;/body&gt;';

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientSetItemSEORequest>
    <Type>1060</Type>
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <AuthID>$authId</AuthID>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
    <ItemID>29246</ItemID>
    <Slug>slug</Slug>
    <SEOTitle>SEO Title</SEOTitle>
    <SEODescription>SEO Description</SEODescription>
    <SEOKeywords>SEO Keywords</SEOKeywords>
    <OldSlug>old slug</OldSlug>
    <Ingredients>$body</Ingredients>
    <Instructions>$body</Instructions>
    <MakeID>1001</MakeID>
    <CategoryIDs>1003,1004,1005,1016</CategoryIDs>
    <Summary>$body</Summary>
    <ManufacturID>1001</ManufacturID>
</ClientSetItemSEORequest>
EOF;
        try {
            $itemsArr = array();
            $result = $client->SendMessage(['Message' => $message]);
            $items = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            dump($message, $result);

            return;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }
}