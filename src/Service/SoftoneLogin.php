<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SoftoneLogin
{
    /**
     * @var SessionInterface
     */
    protected $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
        if ($this->session->has("authID") === false || $this->session->get("authID") === null || !$this->session->get("authID")) {
            $authID = $this->login();
            $this->session->set('authID', $authID);
        }
    }

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
            $result = $client->SendMessage(['Message' => $message]);
            $s1result = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            return (string)$s1result->AuthID;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }
}