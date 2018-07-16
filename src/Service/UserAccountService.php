<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 3/6/2018
 * Time: 3:43 μμ
 */

namespace App\Service;

use App\Entity\WebUser;
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
     * @param $userData
     * @return string
     */
    public function createUser($userData)
    {
        $userId = $this->setClient($userData);
        if ($userId) {
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
            $userXML = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
//            dump($result);
            if ((string)$userXML->IsValid === 'false') {
                return 0;
            } else {
                return (int)$userXML->ID;
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
//            dump($result);
            return ((string)$newsletterData->IsValid === 'true') ? true : false;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    public function getNewsletter($username)
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
    <FilterEmail>null</FilterEmail>
</ClientGetNewsletterRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $newsletterData = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            dump($message, $result);
            if ((int)$newsletterData->RowsCount === 0) {
                return array(
                    'newsletter' => $newsletterData->GetDataRows->GetNewsletterRow->Allow
                );
            }else{
                return array(
                    'newsletter' => false
                );
            }
//            dump($result);
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    public function setAddress($addressData)
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $address = $addressData["username"];

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
    <Username></Username>
    <Password></Password>
    <Email>null</Email>
</ClientGetUsersRequest>
EOF;
        return $message;
    }

    /**
     * @param $username
     * @param $password
     * @return string
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
            if ((int)$userData->RowsCount === 0) {
                return '';
//            } else if ($password === 'null') {
//                return false;
            } else {
                $clientData = $this->getClient($username);
                dump($clientData["name"]);
                dump($userData->GetDataRows->GetUsersRow->ClientID);
//                $this->session->set('anosiaName', $clientData["name"]);
//                $this->session->set('anosiaClientId', (int)$userData->GetDataRows->GetUsersRow->ClientID);
                return $username;
            }
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }

    }

    /**
     * @param string $username
     * @param string $password
     * @return \App\Entity\WebUser
     * @throws \Exception
     */
    public function getUser($username = 'null', $user = null) // remove nulls in production
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
    <Username>$username</Username>
    <Password>null</Password>
    <Email>null</Email>
</ClientGetUsersRequest>
EOF;
        try {
            $userData = array();
            $result = $client->SendMessage(['Message' => $message]);
            $userResponse = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            if ($userResponse === false) {
                return $userData;
            }
            $userXML = $userResponse->GetDataRows->GetUsersRow;
            $user->setUsername($userXML->Username);
            $user->setPassword($userXML->Password);
            $user->setClientId($userXML->ClientID);
            return;
//            return $userData = $this->initializeUser($userXML->GetDataRows->GetUsersRow);
//            if ((int)$userData->RowsCount === 0) {
//                return false;
//            } else {
//                return true;
//            }
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }

    }

    /**
     * @param $userXML
     * @return array
     * @throws \Exception
     */
    private function initializeUser($userXML)
    {
        try {
            $userArr = array(
                'username' => $userXML->Username,
                'password' => $userXML->Password,
                'lastLoginDT' => $userXML->LastLoginDT,
                'clientId' => $userXML->ClientID,
                'userId' => $userXML->ID,
            );
            return $userArr;
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * @param $username
     * @param \App\Entity\WebUser
     * @return array
     * @throws \Exception
     */
    public function getUserInfo($username, $user)
    {
        $userArr = $this->getUser($username, $user);
        $clientArr = $this->getClient($username, $user);
        $newsletterArr = $this->getNewsletter($username);
        $userInfo = array_merge($userArr, $clientArr, $newsletterArr);
        dump($userInfo);
        return $userInfo;
    }

    public function getClient($username, $user)
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetClientsRequest>
    <Type>1042</Type>
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
    <pagesize>1</pagesize>
    <pagenumber>0</pagenumber>
    <ClientID>-1</ClientID>
    <Code>null</Code>
    <Email>$username</Email>
</ClientGetClientsRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $clientResponse = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            if ($clientResponse === false) {
                return;
            }
            dump($result);
            $userXML = $clientResponse->GetDataRows->GetClientsRow;
            $userName = explode(' ', $userXML->NAME);
            $user->setFirstname($userName[0]);
            $user->setLastname($userName[1]);
            $user->setEmail($userXML->EMAIL);
            dump($user);
            return;
//            dump($result);
//            return $clientData = $this->initializeClient($userXML->GetDataRows->GetClientsRow);
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    private function initializeClient($userXML)
    {
        try {
            $userArr = array(
                'address' => $userXML->ADDRESS,
                'name' => $userXML->NAME,
            );
            return $userArr;
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getUsers() // to be deleted in production
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
    <Username>null</Username>
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