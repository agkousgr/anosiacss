<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use SimpleXMLElement;

class SoftoneLogin
{
    public function login()
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientSingleLoginRequest>
    <Type>1018</Type>
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <Username>webuser</Username>
    <Password>password</Password>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
    <BranchID>1000</BranchID>
    <ModuleID>0</ModuleID>
    <RefID>1</RefID>
    <UserID>1</UserID>
</ClientSingleLoginRequest>
EOF;
        try {
//            if ($session->has("authID") === false) {
                $result = $client->SendMessage(['Message' => $message]);
                $s1result = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
//                $session->set("authID", (string)$s1result->AuthID);
//            }
            return (string)$s1result->AuthID;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }
}