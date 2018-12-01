<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class NewsletterService
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
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
     * UserAccountService constructor.
     * @param LoggerInterface $logger
     * @param SessionInterface $session
     */
    public function __construct(LoggerInterface $logger, SessionInterface $session, $s1Credentials)
    {
        $this->logger = $logger;
        $this->session = $session;
        dump($session->get("authID"));
        die();
        $this->authId = $session->get("authID");
        $this->kind = $s1Credentials['kind'];
        $this->domain = $s1Credentials['domain'];
        $this->appId = $s1Credentials['appId'];
        $this->companyId = $s1Credentials['companyId'];
    }

    /**
     * @param $name
     * @param $email
     * @param $referrer
     *
     * @return array
     */
    public function getNewsletter($name, $email, $referrer, $gender, $age)
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);
        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetNewsletterRequest>
    <Type>1013</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <pagesize>10</pagesize>
    <pagenumber>0</pagenumber>
    <Email>$email</Email>
</ClientGetNewsletterRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $newsletterData = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            dump($message, $result);
            $success = false;
            $exist = false;
            if ((int)$newsletterData->RowsCount > 0) {
                if ((string)$newsletterData->GetDataRows->GetNewsletterRow->Allow === 'false') {
                    $this->setNewsletter($name, $email, intval($newsletterData->GetDataRows->GetNewsletterRow->ID), 'true', $referrer, $gender, $age);
                    //                 When sotiris return key in getNewsletter send it with email
                    $success = true;
                    $exist = true;
                }
            } else {
                if ($this->setNewsletter($name, $email, '', '', $referrer, $gender, $age)) {
                    $success = true;
                }
            }
            return [
                'success' => $success,
                'exist' => $exist
            ];
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * @param $name
     * @param $email
     * @param string $key
     * @param string $allow
     * @param $referrer
     * @param string $gender
     * @param string $age
     *
     * @return bool
     */
    public function setNewsletter($name, $email, $key = '', $allow = 'true', $referrer, $gender = '', $age = '')
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientSetNewsletterRequest>
    <Type>1020</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <Key>$key</Key>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <Email>$email</Email>
    <Name>$name</Name>
    <Age>$age</Age>
    <Gender>$gender</Gender>
    <Allow>$allow</Allow>
    <Referrer>$referrer</Referrer>
</ClientSetNewsletterRequest>
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