<?php


namespace App\Service;


class MigrationService
{
    public function saveImage()
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientSetCCCSOPHDiskFileRequest>
    <Type>1058</Type>
    <Kind>1</Kind>
    <Domain>anosiaph</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
    <sodtype>51</sodtype>
    <sosource>null</sosource>
    <TableName>TableName</TableName>
    <TableID>9999</TableID>
    <FileName>SomeFile.txt</FileName>
    <IsPhoto>false</IsPhoto>
    <IsDoc>true</IsDoc>
    <IsMain>false</IsMain>
</ClientSetCCCSOPHDiskFileRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $newsletterData = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
//            dump($message, $result);
            return ((string)$newsletterData->IsValid === 'true') ? true : false;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }
}