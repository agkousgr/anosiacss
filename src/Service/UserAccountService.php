<?php

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
     * @param \App\Entity\WebUser
     *
     * @return int
     */
    private function updateClient($user)
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);
//        dump($user);
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
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <Key>$clientId</Key>
    <AuthID>$this->authId</AuthID>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
    <CODE>C*</CODE>
    <NAME>$name</NAME>
    <BRANCH>1000</BRANCH>
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
//            dump($message, $result);
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
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <Key>$key</Key>
    <AuthID>$this->authId</AuthID>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
    <Email>$username</Email>
    <Name>$name</Name>
    <Allow>true</Allow>
    <Referrer>Registration Form</Referrer>
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
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
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
//            dump($result);
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * @param \App\Entity\WebUser | \App\Entity\Address
     *
     * @return string
     */
    public function getAddress($clientId, $entity, $id=-1)
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetShipAddressRequest>
    <Type>1034</Type>
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
    <ClientID>$clientId</ClientID>
    <ID></ID>
    <ShipAddressID>$id</ShipAddressID>
</ClientGetShipAddressRequest>
EOF;

        try {
            $result = $client->SendMessage(['Message' => $message]);
            $addressData = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            dump($message);
            if ($addressData->RowsCount) {
                $addressesArr = [];
                dump($addressData->GetDataRows);
                $addressXML = $addressData->GetDataRows->GetShipAddressRow;


                $entity->setClient((int)$clientId);
                (null !== $addressXML->ID) ? $entity->setId((int)$addressXML->ID) : $entity->setId(0);
                (null !== $addressXML->Name) ? $entity->setName((string)$addressXML->Name) : $entity->setName('');
                (null !== $addressXML->Address) ? $entity->setAddress((string)$addressXML->Address) : $entity->setAddress('');
                (null !== $addressXML->Zip) ? $entity->setZip((string)$addressXML->Zip) : $entity->setZip('');
                (null !== $addressXML->City) ? $entity->setCity((string)$addressXML->City) : $entity->setCity('');
                (null !== $addressXML->District) ? $entity->setDistrict((string)$addressXML->District) : $entity->setDistrict('');
            }
            dump($addressesArr);

//            return;
            return $addressesArr;
//            dump($result);
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
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
    <ClientID>$clientId</ClientID>
    <ID></ID>
    <ShipAddressID>-1</ShipAddressID>
</ClientGetShipAddressRequest>
EOF;

        try {
            $result = $client->SendMessage(['Message' => $message]);
            $addressData = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            $addressesArr = [];
                dump($message, $addressData);
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
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
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
            dump($message, $result);
            $addressData = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            if ((int)$addressData->ID > 0) {
                return true;
            } else {
                return false;
            }
//            dump($result);
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
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
    <Key>$id</Key>
</ClientDelShipAddressRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            dump($message, $result);
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
            dump($message, $result);
            if ((int)$userData->RowsCount === 0) {
                return '';
//            } else if ($password === 'null') {
//                return false;
            } else {
//                $clientData = $this->getClient($username);
//                dump($clientData["name"]);
//                dump($userData->GetDataRows->GetUsersRow->ClientID);
//                $this->session->set('anosiaName', $clientData["name"]);
                $this->session->set('anosiaClientId', (int)$userData->GetDataRows->GetUsersRow->ClientID);
                return $username;
            }
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }

    }

    /**
     * @param string $username
     * @param \App\Entity\WebUser
     * @param \App\Entity\Address
     *
     * @throws \Exception
     */
    public function getUser($username = 'null', $user, $address = null) // remove nulls in production
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
            $result = $client->SendMessage(['Message' => $message]);
            $userResponse = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            if ($userResponse === false) {
                return;
            }
//            dump($message, $result);
            $userXML = $userResponse->GetDataRows->GetUsersRow;
            if (null !== $address) {
                $address->setClient($userXML->ClientID);
            } else {
//                dump($user);
                (null !== $userXML->Username) ? $user->setUsername($userXML->Username) : $user->setUsername('');
                (null !== $userXML->Password) ? $user->setPassword($userXML->Password) : $user->setUsername('');
                (null !== $userXML->ClientID) ? $user->setClientId($userXML->ClientID) : $user->setUsername('');
            }
            return;
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
        $this->updateClient($user);
        if ($user->isNewsletter() === true)
            $this->updateNewsletter($user);
//        $userInfo = array_merge($userArr, $clientArr, $newsletterArr);
        return;
    }

    /**
     * @param $username
     * @param null|\App\Entity\WebUser
     */
    public function getClient($username, $user = null)
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
//            dump($result);
            $userXML = $clientResponse->GetDataRows->GetClientsRow;
            $userName = explode(' ', $userXML->NAME);
            $user->setFirstname($userName[0]);
            $user->setLastname($userName[1]);
            $user->setEmail($userXML->EMAIL);
            (null !== $userXML->ADDRESS) ? $user->setAddress((string)$userXML->ADDRESS) : $user->setAddress('');
            (null !== $userXML->ZIP) ? $user->setZip((string)$userXML->ZIP) : $user->setZip('');
            (null !== $userXML->CITY) ? $user->setCity((string)$userXML->CITY) : $user->setCity('');
            (null !== $userXML->DISTRICT) ? $user->setDistrict((string)$userXML->DISTRICT) : $user->setDistrict('');
            (null !== $userXML->PHONE01) ? $user->setPhone01((string)$userXML->PHONE01) : $user->setPhone01('');
            (null !== $userXML->AFM) ? $user->setAfm((string)$userXML->AFM) : $user->setAfm('');
            (null !== $userXML->IRS) ? $user->setIrs((string)$userXML->IRS) : $user->setIrs('');
//            dump($user);
            return;
//            dump($result);
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
//            dump($result);
            $userXML = $clientResponse->GetDataRows->GetClientsRow;
            (empty($userXML->ADDRESS)) ? $userMainAddress->setAddress((string)$userXML->ADDRESS) : $userMainAddress->setAddress('');
            (empty($userXML->ZIP)) ? $userMainAddress->setZip((string)$userXML->ZIP) : $userMainAddress->setZip('');
            (null !== $userXML->CITY) ? $userMainAddress->setCity((string)$userXML->CITY) : $userMainAddress->setCity('');
            (null !== $userXML->DISTRICT) ? $userMainAddress->setDistrict((string)$userXML->DISTRICT) : $userMainAddress->setDistrict('');
            (null !== $userXML->PHONE01) ? $userMainAddress->setPhone01((string)$userXML->PHONE01) : $userMainAddress->setPhone01('');
//            $user->setLastname($userName[1]);
//            $user->setEmail($userXML->EMAIL);
//            dump($userMainAddress);
            return;
//            dump($result);
//            return $clientData = $this->initializeClient($userXML->GetDataRows->GetClientsRow);
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
                'newsletter' => $user->getNewsletter(),
                'address' => $user->getAddress(),
                'city' => $user->getCity(),
                'zip' => $user->getZip(),
                'district' => $user->getDistrict(),
                'phone01' => $user->getPhone01(),
                'email' => $user->getEmail(),
            );
//            $addressArr = array(
//                'addresses' => array(
//                    'id' => $address->getId(),
//                    'name' => $address->getName(),
//                    'address' => $user->getAddress(),
//                    'city' => $user->getCity(),
//                    'zip' => $user->getZip(),
//                    'district' => $user->getDistrict(),
//                )
//            );
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
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
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
//            dump($message, $userData->GetDataRows->GetUsersRow->ClientID);
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
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
    <pagesize>100</pagesize>
    <pagenumber>0</pagenumber>
    <FilterEmail>null</FilterEmail>
</ClientGetNewsletterRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $newsletterData = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
//            dump($result);
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    public function getOrders($clientId)
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetOrdersRequest>
    <Type>1048</Type>
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
    <CustomerID>$clientId</CustomerID>
    <OrderID>null</OrderID>
    <Number>null</Number>
    <EshopNumber>null</EshopNumber>
</ClientGetOrdersRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $ordersData = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            dump($message, $result);
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }
}