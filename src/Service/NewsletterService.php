<?php


namespace App\Service;


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
     *
     * @return bool
     */
    public function getNewsletter($name, $email)
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
    <pagesize>1</pagesize>
    <pagenumber>0</pagenumber>
    <Email>$email</Email>
</ClientGetNewsletterRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $newsletterData = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            if ((int)$newsletterData->RowsCount > 0) {
                if ((string)$newsletterData->GetDataRows->GetNewsletterRow->Allow === 'false') {
                    $this->setNewsletter(); // changes HERE
                } else {
//                    $user->setNewsletter(false);
//                    $user->setNewsletterId('');
                }
            } else {
//                $user->setNewsletter(false);
            }
            return;
//            dump($result);
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * @param $userData
     * @return true|false
     */
    public function setNewsletter($userData)
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
    <Email>$username</Email>
    <Name>$name</Name>
</ClientSetNewsletterRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $newsletterData = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            dump($message, $result);
            return ((string)$newsletterData->IsValid === 'true') ? true : false;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }
}