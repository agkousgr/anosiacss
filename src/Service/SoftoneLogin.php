<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SoftoneLogin
{
    /**
     * @var SessionInterface
     */
    protected $session;

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
     * @var string
     */
    private $refId;

    /**
     * @var string
     */
    private $userId;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    public function __construct(SessionInterface $session, $s1Credentials)
    {
        $this->session = $session;
        $this->kind = $s1Credentials['kind'];
        $this->domain = $s1Credentials['domain'];
        $this->appId = $s1Credentials['appId'];
        $this->companyId = $s1Credentials['companyId'];
        $this->refId = $s1Credentials['refId'];
        $this->userId = $s1Credentials['userId'];
        $this->username = $s1Credentials['username'];
        $this->password = $s1Credentials['password'];
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
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <Username>$this->username</Username>
    <Password>$this->password</Password>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <BranchID>1000</BranchID>
    <ModuleID>0</ModuleID>
    <RefID>10</RefID>
    <UserID>10</UserID>
</ClientSingleLoginRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $s1result = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            dump($result);
            return (string)$s1result->AuthID;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }
}