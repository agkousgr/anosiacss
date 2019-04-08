<?php

namespace App\Service;

use App\Entity\Checkout;
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
     * @var ProductService
     */
    private $productService;

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
     * @var \SoapClient
     */
    private $client;

    /**
     * UserAccountService constructor.
     * @param LoggerInterface $logger
     * @param SessionInterface $session
     * @param ProductService $productService
     */
    public function __construct(LoggerInterface $logger, SessionInterface $session, ProductService $productService, $s1Credentials)
    {
        $this->logger = $logger;
        $this->session = $session;
        $this->authId = $session->get("authID");
        $this->productService = $productService;
        $this->kind = $s1Credentials['kind'];
        $this->domain = $s1Credentials['domain'];
        $this->appId = $s1Credentials['appId'];
        $this->companyId = $s1Credentials['companyId'];
        $this->client = new \SoapClient('https://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);
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

        if ($userData instanceof Checkout) {
            $name = $userData->getFirstname() . ' ' . $userData->getLastname();
            $username = $userData->getUsername();
        } else {
            $name = $userData["firstname"] . ' ' . $userData["lastname"];
            $username = $userData["username"];
        }
        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientSetClientRequest>
    <Type>1028</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <CODE>C*</CODE>
    <NAME>$name</NAME>
    <BRANCH>1001</BRANCH>
    <ADDRESS></ADDRESS>
    <ZIP></ZIP>
    <CITY></CITY>
    <DISTRICT></DISTRICT>
    <PHONE01></PHONE01>
    <PHONE02></PHONE02>
    <EMAIL>$username</EMAIL>
</ClientSetClientRequest>
EOF;

//        return 0;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $userXML = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
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
     * @param \App\Entity\WebUser
     *
     * @return int
     */
    private function updateClient($user)
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);
        $name = $user->getFirstname() . ' ' . $user->getLastname();
        $clientId = $user->getClientId();
        $email = $user->getEmail();
        $address = $user->getAddress();
        $zip = $user->getZip();
        $city = $user->getCity();
        $district = $user->getDistrict();
        $phone01 = $user->getPhone01();

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientSetClientRequest>
    <Type>1028</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <Key>$clientId</Key>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <CODE>C*</CODE>
    <NAME>$name</NAME>
    <BRANCH>1001</BRANCH>
    <ADDRESS>$address</ADDRESS>
    <ZIP>$zip</ZIP>
    <CITY>$city</CITY>
    <DISTRICT>$district</DISTRICT>
    <PHONE01>$phone01</PHONE01>
    <PHONE02></PHONE02>
    <EMAIL>$email</EMAIL>
</ClientSetClientRequest>
EOF;

        try {
            $result = $client->SendMessage(['Message' => $message]);
            $userXML = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
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
        if ($userData instanceof Checkout) {
            $password = password_hash('guest#$', PASSWORD_DEFAULT);
            $username = $userData->getUsername();
            $verificationToken = 'null';
        } else {
            $password = password_hash($userData["password"], PASSWORD_DEFAULT);
            $username = $userData["username"];
            $verificationToken = $userData['token'];
        }
        $lastLogin = date('Y-m-d') . 'T' . date('H:i:s');

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientSetUserRequest>
    <Type>1022</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <Key></Key>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <Username>$username</Username>
    <Password>$password</Password>
    <ClientID>$userId</ClientID>
    <LastLoginDT>$lastLogin</LastLoginDT>
    <IsActive>0</IsActive>
    <PasswordRequestCounter>0</PasswordRequestCounter>
    <VerificationToken>$verificationToken</VerificationToken>
</ClientSetUserRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $userResult = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            if ((string)$userResult->ErrorCode === 'None') {
                if ($userData instanceof Checkout) {
                    $userData->setClientId($userResult->ID);
                }
                return true;
            } else {
                return false;
            }
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    public function updateUserAfterForgotPassword(array $userData, bool $hasNulls = false)
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);
        $id = $userData['ID'];
        $username = $userData['Username'];
        $password = $userData['Password'];
        $clientId = $userData['ClientID'];
        $lastLogin = $userData['LastLoginDT'];
        $counter = $userData['PasswordRequestCounter'];
        $isActive = $userData['IsActive'];
        if ($hasNulls) {
            $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientSetUserRequest>
    <Type>1022</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <Key>$id</Key>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <Username>$username</Username>
    <Password>$password</Password>
    <ClientID>$clientId</ClientID>
    <LastLoginDT>$lastLogin</LastLoginDT>
    <PasswordRequestCounter>$counter</PasswordRequestCounter>
    <IsActive>$isActive</IsActive>
</ClientSetUserRequest>
EOF;
        } else {
            $token = $userData['ConfirmationToken'];
            $requestedAt = $userData['PasswordRequestDT'];
            $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientSetUserRequest>
    <Type>1022</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <Key>$id</Key>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <Username>$username</Username>
    <Password>$password</Password>
    <ClientID>$clientId</ClientID>
    <LastLoginDT>$lastLogin</LastLoginDT>
    <PasswordRequestCounter>$counter</PasswordRequestCounter>
    <IsActive>$isActive</IsActive>
    <ConfirmationToken>$token</ConfirmationToken>
    <PasswordRequestDT>$requestedAt</PasswordRequestDT>
    <ConfirmationRequestDT>$requestedAt</ConfirmationRequestDT>
</ClientSetUserRequest>
EOF;
        }

        try {
            $result = $client->SendMessage(['Message' => $message]);
            $userResult = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));

            return (string)$userResult->ErrorCode === 'None';
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * @param \App\Entity\WebUser
     * @return true|false
     */
    public function updateNewsletter($user)
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $name = $user->getFirstname() . ' ' . $user->getLastname();
        $username = $user->getUsername();
        $key = $user->getNewsletterId();

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
    <Email>$username</Email>
    <Name>$name</Name>
    <Allow>true</Allow>
    <Referrer>Registration Form</Referrer>
</ClientSetNewsletterRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $newsletterData = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));

            return ((string)$newsletterData->IsValid === 'true') ? true : false;
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
        if ($userData instanceof Checkout) {
            if ($userData->isNewsletter() === false or null === $userData->isNewsletter()) {
                return true;
            }
            $name = $userData->getFirstname() . ' ' . $userData->getLastname();
            $username = $userData->getUsername();
        } else {
            $name = $userData["firstname"] . ' ' . $userData["lastname"];
            $username = $userData["username"];
        }

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientSetNewsletterRequest>
    <Type>1020</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <Key></Key>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <Email>$username</Email>
    <Name>$name</Name>
</ClientSetNewsletterRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $newsletterData = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));

            return ((string)$newsletterData->IsValid === 'true');
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * @param $username
     * @param \App\Entity\WebUser
     */
    public function getNewsletter($username, $user)
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
    <pagesize>1</pagesize>
    <pagenumber>0</pagenumber>
    <Email>$username</Email>
</ClientGetNewsletterRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $newsletterData = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            if ((int)$newsletterData->RowsCount > 0) {
                if ((string)$newsletterData->GetDataRows->GetNewsletterRow->Allow === 'true') {
                    $user->setNewsletter(true);
                    $user->setNewsletterId((string)$newsletterData->GetDataRows->GetNewsletterRow->ID);
                } else {
                    $user->setNewsletter(false);
                    $user->setNewsletterId('');
                }
            } else {
                $user->setNewsletter(false);
            }
            return;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * @param \App\Entity\WebUser | \App\Entity\Address
     *
     * @return string
     */
    public function getAddress($clientId, $entity, $id = -1)
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetShipAddressRequest>
    <Type>1034</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <ClientID>$clientId</ClientID>
    <ID></ID>
    <ShipAddressID>$id</ShipAddressID>
</ClientGetShipAddressRequest>
EOF;

        try {
            $result = $client->SendMessage(['Message' => $message]);
            $addressData = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            if ($addressData->RowsCount) {
                $addressesArr = [];
                $addressXML = $addressData->GetDataRows->GetShipAddressRow;

                $entity->setClient((int)$clientId);
                (null !== $addressXML->ID) ? $entity->setId((int)$addressXML->ID) : $entity->setId(0);
                (null !== $addressXML->Name) ? $entity->setName((string)$addressXML->Name) : $entity->setName('');
                (null !== $addressXML->Address) ? $entity->setAddress((string)$addressXML->Address) : $entity->setAddress('');
                (null !== $addressXML->Zip) ? $entity->setZip((string)$addressXML->Zip) : $entity->setZip('');
                (null !== $addressXML->City) ? $entity->setCity((string)$addressXML->City) : $entity->setCity('');
                (null !== $addressXML->District) ? $entity->setDistrict((string)$addressXML->District) : $entity->setDistrict('');
            }

//            return;
            return $addressesArr;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * @param \App\Entity\WebUser | \App\Entity\Address
     *
     * @return string
     */
    public function getAddresses($clientId)
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetShipAddressRequest>
    <Type>1034</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <ClientID>$clientId</ClientID>
    <ID></ID>
    <ShipAddressID>-1</ShipAddressID>
</ClientGetShipAddressRequest>
EOF;

        try {
            $result = $client->SendMessage(['Message' => $message]);
            $addressData = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            $addressesArr = [];
            if ($addressData->RowsCount) {
                foreach ($addressData->GetDataRows->GetShipAddressRow as $getDataRow) {
                    $addressXML = $getDataRow;

                    $addressesArr['addresses'][] = array(
                        'client' => (int)$clientId,
                        'id' => (null !== $addressXML->ID) ? (int)$addressXML->ID : 0,
                        'name' => (null !== $addressXML->Name) ? (string)$addressXML->Name : '',
                        'address' => (null !== $addressXML->Address) ? (string)$addressXML->Address : '',
                        'zip' => (null !== $addressXML->Zip) ? (string)$addressXML->Zip : '',
                        'city' => (null !== $addressXML->City) ? (string)$addressXML->City : '',
                        'district' => (null !== $addressXML->District) ? (string)$addressXML->District : ''
                    );
                }
            }

            return $addressesArr;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * @param \App\Entity\Address
     *
     * @return string
     */
    public function setAddress($Address)
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $id = ($Address->getId()) ?: '';
        $name = $Address->getName();
        $clientId = $Address->getClient();
        $address = $Address->getAddress();
        $zip = $Address->getZip();
        $city = $Address->getCity();
        $district = $Address->getDistrict();

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientSetShipAddressRequest>
    <Type>1036</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <Key>$id</Key>
    <ClientID>$clientId</ClientID>
    <Address>$address</Address>
    <Zip>$zip</Zip>
    <City>$city</City>
    <District>$district</District>
    <Name>$name</Name>
</ClientSetShipAddressRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $addressData = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            if ((int)$addressData->ID > 0) {
                return true;
            } else {
                return false;
            }
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * @param $id
     * @return bool
     */
    public function deleteAddress($id)
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientDelShipAddressRequest>
    <Type>1052</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <Key>$id</Key>
</ClientDelShipAddressRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $addressData = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            if ((string)$addressData->IsValid === 'true') {
                return true;
            }
            return false;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
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
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <pagesize>1</pagesize>
    <pagenumber>0</pagenumber>
    <Username>$username</Username>
    <Password>null</Password>
    <Email>null</Email>
    <ConfirmationToken>null</ConfirmationToken>
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
                if (password_verify($password, strval($userData->GetDataRows->GetUsersRow->Password))) {
                    $this->session->set('anosiaClientId', (int)$userData->GetDataRows->GetUsersRow->ClientID);
                    return $username;
                } else {
                    return '';
                }
//                $clientData = $this->getClient($username);
//                $this->session->set('anosiaName', $clientData["name"]);
            }
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }

    }

    /**
     * @param string $username
     * @param null   $user
     * @param null   $address
     *
     * @return \SimpleXMLElement|bool
     */
    public function getUser($username = 'null', $email = 'null', $user = null, $address = null) // remove nulls in production
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetUsersRequest>
    <Type>1015</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <pagesize>1</pagesize>
    <pagenumber>0</pagenumber>
    <Username>$username</Username>
    <Password>null</Password>
    <Email>$email</Email>
    <ConfirmationToken>null</ConfirmationToken>
</ClientGetUsersRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $userResponse = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            if ($userResponse === false) {
                return false;
            }
            $userXML = $userResponse->GetDataRows->GetUsersRow;
            if (null !== $address) {
                $address->setClient($userXML->ClientID);
            } else {
                if (null !== $user) {
                    (null !== $userXML->Username) ? $user->setUsername($userXML->Username) : $user->setUsername('');
                    (null !== $userXML->Username) ? $user->setPassword($userXML->Password) : $user->setPassword('');
                    (null !== $userXML->Username) ? $user->setClientId($userXML->ClientID) : $user->setClientId('');
                }

            }

            return $userXML;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * @param string $token
     *
     * @return bool|\SimpleXMLElement
     *
     * @throws \SoapFault
     */
    public function getUserByToken(string $token)
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetUsersRequest>
    <Type>1015</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <pagesize>1</pagesize>
    <pagenumber>0</pagenumber>
    <Username>null</Username>
    <Password>null</Password>
    <Email>null</Email>
    <ConfirmationToken>$token</ConfirmationToken>
</ClientGetUsersRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $userResponse = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            if ($userResponse === false) {
                return false;
            }
            $userXML = $userResponse->GetDataRows->GetUsersRow;

            return $userXML;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * @param string $token
     *
     * @return bool|\SimpleXMLElement
     *
     * @throws \SoapFault
     */
    public function getUserByVerificationToken(string $token)
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetUsersRequest>
    <Type>1015</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <pagesize>1</pagesize>
    <pagenumber>0</pagenumber>
    <Username>null</Username>
    <Password>null</Password>
    <Email>null</Email>
    <VerificationToken>$token</VerificationToken>
</ClientGetUsersRequest>
EOF;
        dump($message);
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $userResponse = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            if ($userResponse === false) {
                return false;
            }
            $userXML = $userResponse->GetDataRows->GetUsersRow;

            return $userXML;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    public function activateUser($user)
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $id = $user->ID;
        $username = $user->Username;
        $password = $user->Password;
        $clientId = $user->ClientID;
        $lastLogin = $user->LastLoginDT;
        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientSetUserRequest>
    <Type>1022</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <Key>$id</Key>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <Username>$username</Username>
    <Password>$password</Password>
    <ClientID>$clientId</ClientID>
    <LastLoginDT>$lastLogin</LastLoginDT>
    <IsActive>1</IsActive>
    <PasswordRequestCounter>0</PasswordRequestCounter>
</ClientSetUserRequest>
EOF;
        dump($message);
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $userResult = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));

            return (string)$userResult->ErrorCode === 'None';
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * @param $username
     * @param \App\Entity\WebUser
     * @return array
     * @throws \Exception
     */
    public function getUserInfo($username, $user, $address = null)
    {
        $this->getUser($username, $user);
        $this->getClient($username, $user);
        $addressesArr = $this->getAddresses($user->getClientId());
        $this->getNewsletter($username, $user);
        return $this->initializeClient($user, $addressesArr);
    }

    /**
     * @param $username
     * @param \App\Entity\Address
     * @throws \Exception
     */
    public function getAddressInfo($username, $user, $address)
    {
        $this->getUser($username, $user);
        $this->getAddress($user->getClientId(), $address);
    }

    /**
     * @param \App\Entity\WebUser
     * @param $userMainAddressData
     */
    public function updateUserInfo($user)
    {
        $clientId = $this->updateClient($user);

        if ($user->isNewsletter() === true)
            $this->updateNewsletter($user);
//        $userInfo = array_merge($userArr, $clientArr, $newsletterArr);
        if ($user instanceof Checkout)
            return $clientId;
        return;
    }

    /**
     * @param $username
     * @param null|\App\Entity\WebUser
     */
    public function getClient($username, $user = null)
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $clientId = $user->getClientId();

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetClientsRequest>
    <Type>1042</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <pagesize>1</pagesize>
    <pagenumber>0</pagenumber>
    <ClientID>$clientId</ClientID>
    <Code>null</Code>
    <Email>null</Email>
</ClientGetClientsRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $clientResponse = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            if ($clientResponse === false) {
                return;
            }
            $userXML = $clientResponse->GetDataRows->GetClientsRow;

            list($firstname, $lastname) = explode(' ', $userXML->NAME);
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setEmail($userXML->EMAIL);
            (null !== $userXML->ADDRESS) ? $user->setAddress((string)$userXML->ADDRESS) : $user->setAddress('');
            (null !== $userXML->ZIP) ? $user->setZip((string)$userXML->ZIP) : $user->setZip('');
            (null !== $userXML->CITY) ? $user->setCity((string)$userXML->CITY) : $user->setCity('');
            (null !== $userXML->DISTRICT) ? $user->setDistrict((string)$userXML->DISTRICT) : $user->setDistrict('');
            (null !== $userXML->PHONE01) ? $user->setPhone01((string)$userXML->PHONE01) : $user->setPhone01('');
            (null !== $userXML->AFM) ? $user->setAfm((string)$userXML->AFM) : $user->setAfm('');
            (null !== $userXML->IRS) ? $user->setIrs((string)$userXML->IRS) : $user->setIrs('');

            return;
//            return $clientData = $this->initializeClient($userXML->GetDataRows->GetClientsRow);
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * @param $username
     * @param \App\Entity\Address
     */
    public function getMainAddress($username, $userMainAddress)
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetClientsRequest>
    <Type>1042</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
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
            $userXML = $clientResponse->GetDataRows->GetClientsRow;
            (empty($userXML->ADDRESS)) ? $userMainAddress->setAddress((string)$userXML->ADDRESS) : $userMainAddress->setAddress('');
            (empty($userXML->ZIP)) ? $userMainAddress->setZip((string)$userXML->ZIP) : $userMainAddress->setZip('');
            (null !== $userXML->CITY) ? $userMainAddress->setCity((string)$userXML->CITY) : $userMainAddress->setCity('');
            (null !== $userXML->DISTRICT) ? $userMainAddress->setDistrict((string)$userXML->DISTRICT) : $userMainAddress->setDistrict('');
            (null !== $userXML->PHONE01) ? $userMainAddress->setPhone01((string)$userXML->PHONE01) : $userMainAddress->setPhone01('');
//            $user->setLastname($userName[1]);
//            $user->setEmail($userXML->EMAIL);
            return;
//            return $clientData = $this->initializeClient($userXML->GetDataRows->GetClientsRow);
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    public function checkIfUserExist($username)
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetUsersRequest>
    <Type>1015</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <pagesize>1</pagesize>
    <pagenumber>0</pagenumber>
    <Username>$username</Username>
    <Password>null</Password>
    <Email>null</Email>
</ClientGetUsersRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $clientResponse = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            if ($clientResponse->RowsCount > 0) {
                return true;
            }
            return false;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * @param \App\Entity\WebUser
     * @param \App\Entity\Address
     *
     * @return array
     * @throws \Exception
     */
    private function initializeClient($user, $addressArr)
    {
        try {
            $userArr = array(
                'username' => $user->getUsername(),
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'clientId' => $user->getClientId(),
                'newsletterId' => $user->getNewsletterId(),
                'newsletter' => $user->isNewsletter(),
                'address' => $user->getAddress(),
                'city' => $user->getCity(),
                'zip' => $user->getZip(),
                'district' => $user->getDistrict(),
                'phone01' => $user->getPhone01(),
                'email' => $user->getEmail(),
            );

            return array_merge($userArr, $addressArr);
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getClientId($username) // to be deleted in production
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetUsersRequest>
    <Type>1015</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <pagesize>1</pagesize>
    <pagenumber>0</pagenumber>
    <Username>$username</Username>
    <Password>null</Password>
    <Email>null</Email>
</ClientGetUsersRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $userData = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            if ((int)$userData->RowsCount > 0) {
                return $userData->GetDataRows->GetUsersRow->ClientID;
            } else {
                return 0;
            }
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }

    }

    public function getAllNewsletters()
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
    <pagesize>100</pagesize>
    <pagenumber>0</pagenumber>
    <FilterEmail>null</FilterEmail>
</ClientGetNewsletterRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $newsletterData = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * @param $clientId
     * @return int|\SimpleXMLElement
     */
    public function getOrders($clientId)
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetOrdersRequest>
    <Type>1048</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <CustomerID>$clientId</CustomerID>
    <OrderID>null</OrderID>
    <Number>null</Number>
    <EshopNumber>null</EshopNumber>
</ClientGetOrdersRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $ordersData = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));

            if ((int)$ordersData->RowsCount > 0) {
                return $ordersData->GetDataRows->GetOrdersRow;
            } else {
                return 0;
            }
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    public function getOrder($clientId, $orderId)
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetOrderRequest>
    <Type>1050</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <CustomerID>$clientId</CustomerID>
    <OrderID>$orderId</OrderID>
    <Number>null</Number>
    <EshopNumber>null</EshopNumber>
</ClientGetOrderRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $orderData = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));

            if ((int)$orderData->RowsCount > 0) {
                $expensesArr = $this->initializeExpenses($orderData->GetDataRows->GetOrderRow->Expenses);
                $itemsArr = $this->initializeItems($orderData->GetDataRows->GetOrderRow->Items);
                $orderArr = $this->initializeOrder($orderData->GetDataRows->GetOrderRow);
                $orderArr['expenses'] = $expensesArr;
                $orderArr['items'] = $itemsArr;

                return $orderArr;
            } else {
                return 0;
            }
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }


    public function getCoupon($voucherId = -1, $voucherCode = 'null')
    {
        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetVoucherRequest>
    <Type>1072</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <VoucherID>$voucherId</VoucherID>
    <Code>$voucherCode</Code>
</ClientGetVoucherRequest>
EOF;
        try {
            $result = $this->client->SendMessage(['Message' => $message]);
            $items = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            $couponDisc = 0;
            if ($items->GetDataRows->GetVoucherRow) {
                //Todo: put all coupon session in one variable
                if (intval($items->GetDataRows->GetVoucherRow->Value) > 0) {
                    $couponDisc = $items->GetDataRows->GetVoucherRow->Value;
                } elseif (intval($items->GetDataRows->GetVoucherRow->Percentage) > 0) {
                    $couponDisc = $items->GetDataRows->GetVoucherRow->Percentage . '%';
                }
            }
            return $couponDisc;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * @param $orderExpenseRow
     * @return array
     * @throws \Exception
     */
    private function initializeExpenses($orderExpenseRow)
    {
        try {
            $expensesArr = [];
            foreach ($orderExpenseRow as $expenseRow) {
                foreach ($expenseRow as $item) {
                    $expensesArr[] = array(
                        'expenseId' => $item->ExpenseID,
                        'expenceCost' => $item->ExpenseValue
                    );
                }
            }
            return $expensesArr;
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * @param $items
     * @return array
     * @throws \Exception
     */
    private function initializeItems($items)
    {
        try {
            $itemsArr = [];
            foreach ($items as $itemRow) {
                foreach ($itemRow as $item) {
                    $itemsOrder = array(
                        'itemIndex' => $item->ItemIndex,
                        'itemId' => $item->ItemID,
                        'quantity' => $item->ItemQuantity,
                        'itemPrice' => $item->ItemPrice
                    );
                    $itemData = $this->productService->getItems($item->ItemID, $keyword = 'null', 1, $sortBy = 'null', $isSkroutz = -1, $makeId = 'null', $priceRange = 'null');
                    $itemsArr[] = array_merge($itemData, $itemsOrder);
                }
            }
            return $itemsArr;
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }


    private function initializeOrder($order)
    {
        try {
            $orderArr = array(
                'orderId' => $order->ID,
                'orderNo' => $order->EshopNumber,
                'status' => $order->Status,
                'paymentType' => $order->PayTypeID,
                'shippingType' => $order->ShipmentTypeID,
                'shipAddress' => $order->ShipAddress,
                'shipCity' => $order->ShipCity,
                'shipDistrict' => $order->ShipDistrict,
                'shipZip' => $order->ShipZip,
                'comments' => (null !== $order->Comments) ? $order->Comments : '',
                'voucherId' => $order->VoucherID
            );

            return $orderArr;
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}
