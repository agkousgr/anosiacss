<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 3/6/2018
 * Time: 3:43 μμ
 */

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class UserAccountService
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
     * @param $username
     * @return true|false
     */
    private function checkIfUserExists($username)
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetUsersRequest>
    <Type>1015</Type>
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
    <pagesize>10</pagesize>
    <pagenumber>0</pagenumber>
    <Username>$username</Username>
    <Password>null</Password>
    <Email>null</Email>
</ClientGetUsersRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $userData = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            dump($result);
            if ((int)$userData->RowsCount === 0) {
                return false;
            } else {
                return true;
            }
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }

    }

    /**
     * @param $userData
     * @return string
     */
    public function createUser($userData)
    {
        dump($userData);
        if (false === $this->checkIfUserExists($userData["username"])) {
            $userId = $this->setClient($userData);
            if ($userId) {
                dump($userId);
                if ($this->setUser($userData, $userId) === true) {
                    if ($this->setNewsletter($userData)) {
                        return 'Success';
                    } else {
                        return 'Error SetUser';
                    }
                } else {
                    return 'Error SetNewsletter';
                }
            } else {
                return 'Error SetClient';
            }
        }
        return 'User exists';
    }

    /**
     * @param $userData
     * @return int
     */
    private function setClient($userData)
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $name = $userData["firstname"] . ' ' . $userData["lastname"];
        $username = $userData["username"];
        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientSetClientRequest>
    <Type>1028</Type>
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
    <CODE>C*</CODE>
    <NAME>$name</NAME>
    <BRANCH>1000</BRANCH>
    <ADDRESS></ADDRESS>
    <ZIP></ZIP>
    <CITY></CITY>
    <DISTRICT></DISTRICT>
    <PHONE01></PHONE01>
    <PHONE02></PHONE02>
    <EMAIL>$username</EMAIL>
</ClientSetClientRequest>
EOF;

//        dump($message);
//        return 0;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $userData = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            dump($result);
            if ((string)$userData->IsValid === 'false') {
                return 0;
            } else {
                return (int)$userData->ID;
            }
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * @param $userData
     * @param $userId
     * @return bool
     */
    private function setUser($userData, $userId)
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $password = $userData["password"];
        $username = $userData["username"];
        $lastLogin = date('Y-m-d') . 'T' . date('H:i:s');

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientSetUserRequest>
    <Type>1022</Type>
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <Key></Key>
    <AuthID>$this->authId</AuthID>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
    <Username>$username</Username>
    <Password>$password</Password>
    <ClientID>$userId</ClientID>
    <LastLoginDT>$lastLogin</LastLoginDT>
</ClientSetUserRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $userData = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            dump($result);
            if ((string)$userData->ErrorCode === 'None') {
                return true;
            } else {
                return false;
            }
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

        $name = $userData["firstname"] . ' ' . $userData["lastname"];
        $username = $userData["username"];

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientSetNewsletterRequest>
    <Type>1020</Type>
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <Key></Key>
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
            dump($result);
            return ((string)$newsletterData->IsValid === 'true') ? true : false;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * @param $username
     * @param $password
     * @return bool
     */
    public function login($username, $password)
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetUsersRequest>
    <Type>1015</Type>
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
    <pagesize>1</pagesize>
    <pagenumber>0</pagenumber>
    <Username>$username</Username>
    <Password>$password</Password>
    <Email>null</Email>
</ClientGetUsersRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $userData = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            dump($result);
            if ((int)$userData->RowsCount === 0) {
                return false;
            } else {
                $this->session->set("anosiaUser", $username);
                return true;
            }
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }

    }

    public function getUsers()
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetUsersRequest>
    <Type>1015</Type>
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
    <pagesize>20</pagesize>
    <pagenumber>0</pagenumber>
    <Username>jkravaritis@cloudon.gr</Username>
    <Password>null</Password>
    <Email>null</Email>
</ClientGetUsersRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $userData = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            dump($result);
            if ((int)$userData->RowsCount === 0) {
                return false;
            } else {
                return true;
            }
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }

    }
}