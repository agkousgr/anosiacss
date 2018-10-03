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
     * UserAccountService constructor.
     * @param LoggerInterface $logger
     * @param SessionInterface $session
     */
    public function __construct(LoggerInterface $logger, SessionInterface $session)
    {
        $this->logger = $logger;
        $this->session = $session;
        $this->authId = $session->get("authID");
    }

    /**
     * @param $name
     * @param $email
     * @param $referrer
     *
     * @return array
     */
    public function getNewsletter($name, $email, $referrer)
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);
        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetNewsletterRequest>
    <Type>1013</Type>
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
    <pagesize>10</pagesize>
    <pagenumber>0</pagenumber>
    <Email>$email</Email>
</ClientGetNewsletterRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $newsletterData = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
//            dump($message, $result);
            $success = false;
            $exist = false;
            if ((int)$newsletterData->RowsCount > 0) {
                if ((string)$newsletterData->GetDataRows->GetNewsletterRow->Allow === 'false') {
                    $this->setNewsletter($name, $email, intval($newsletterData->GetDataRows->GetNewsletterRow->ID), 'true', $referrer);
                    //                 When sotiris return key in getNewsletter send it with email
                    $success = true;
                    $exist = true;
                }
            } else {
                if ($this->setNewsletter($name, $email, '', '', $referrer)) {
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
     * @param $key
     * @param $allow
     *
     * @return true|false
     */
    public function setNewsletter($name, $email, $key = '', $allow = 'true', $referrer)
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientSetNewsletterRequest>
    <Type>1020</Type>
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <Key>$key</Key>
    <AuthID>$this->authId</AuthID>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
    <Email>$email</Email>
    <Name>$name</Name>
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