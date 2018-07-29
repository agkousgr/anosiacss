<?php

namespace App\Service;

use App\Entity\WebUser;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CheckoutService
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
    private $username;

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
        $this->username = $session->get('anosiaUser');
    }


    /**
     * @param \App\Entity\Checkout
     * @return array
     * @throws \Exception
     */
    public function getUserInfo($checkout, $address = null)
    {
        $this->getUser($checkout);
        $this->getClient($checkout);
        $this->getAddress($checkout->getClientId(), $address);
        $this->getNewsletter($checkout);
        $checkout->setNextPage(1);
        $checkout->setSeries('7021');
        $checkout->setShippingType('1000');
        $checkout->setPaymentType('1000');
        $checkout->setComments('');
        $checkout->setAgreeTerms(false);
        return $this->initializeClient($checkout, $address);
    }

    /**
     * @param \App\Entity\Checkout
     * @param \App\Entity\Address
     *
     * @throws \Exception
     */
    public function getUser($checkout, $address = null) // remove nulls in production
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
    <Username>$this->username</Username>
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
            $userXML = $userResponse->GetDataRows->GetUsersRow;
            if (null !== $address) {
                $address->setClient($userXML->ClientID);
            } else {
                (null !== $userXML->Username) ? $checkout->setUsername($userXML->Username) : $checkout->setUsername('');
                (null !== $userXML->Password) ? $checkout->setPassword($userXML->Password) : $checkout->setUsername('');
                (null !== $userXML->ClientID) ? $checkout->setClientId($userXML->ClientID) : $checkout->setUsername('');
            }
            return;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * @param null|\App\Entity\Checkout
     */
    public function getClient($checkout = null)
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
    <Email>$this->username</Email>
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
            $checkout->setFirstname($userName[0]);
            $checkout->setLastname($userName[1]);
            $checkout->setEmail($userXML->EMAIL);
            (null !== $userXML->ADDRESS) ? $checkout->setAddress((string)$userXML->ADDRESS) : $checkout->setAddress('');
            (null !== $userXML->ZIP) ? $checkout->setZip((string)$userXML->ZIP) : $checkout->setZip('');
            (null !== $userXML->CITY) ? $checkout->setCity((string)$userXML->CITY) : $checkout->setCity('');
            (null !== $userXML->DISTRICT) ? $checkout->setDistrict((string)$userXML->DISTRICT) : $checkout->setDistrict('');
            (null !== $userXML->PHONE01) ? $checkout->setPhone01((string)$userXML->PHONE01) : $checkout->setPhone01('');
            (null !== $userXML->AFM) ? $checkout->setAfm((string)$userXML->AFM) : $checkout->setAfm('');
            (null !== $userXML->IRS) ? $checkout->setIrs((string)$userXML->IRS) : $checkout->setIrs('');
//            dump($user);
            return;
//            dump($result);
//            return $clientData = $this->initializeClient($userXML->GetDataRows->GetClientsRow);
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * @param \App\Entity\WebUser | \App\Entity\Address
     *
     * @return string
     */
    public function getAddress($clientId, $entity)
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
</ClientGetShipAddressRequest>
EOF;

        try {
            $result = $client->SendMessage(['Message' => $message]);
            $addressData = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            dump($message, $result);
            $addressXML = $addressData->GetDataRows->GetShipAddressRow;
            $entity->setClient((int)$clientId);
            (null !== $addressXML->ID) ? $entity->setId((int)$addressXML->ID) : $entity->setId(0);
            (null !== $addressXML->Name) ? $entity->setName((string)$addressXML->Name) : $entity->setName('');
            (null !== $addressXML->Address) ? $entity->setAddress((string)$addressXML->Address) : $entity->setAddress('');
            (null !== $addressXML->Zip) ? $entity->setZip((string)$addressXML->Zip) : $entity->setZip('');
            (null !== $addressXML->City) ? $entity->setCity((string)$addressXML->City) : $entity->setCity('');
            (null !== $addressXML->District) ? $entity->setDistrict((string)$addressXML->District) : $entity->setDistrict('');
            return;
//            dump($result);
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * @param \App\Entity\Checkout
     */
    public function getNewsletter($checkout)
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
    <Email>$this->username</Email>
</ClientGetNewsletterRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $newsletterData = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            if ((int)$newsletterData->RowsCount > 0) {
                if ((string)$newsletterData->GetDataRows->GetNewsletterRow->Allow === 'true') {
                    $checkout->setNewsletter(true);
                    $checkout->setNewsletterId((string)$newsletterData->GetDataRows->GetNewsletterRow->ID);
                } else {
                    $checkout->setNewsletter(false);
                    $checkout->setNewsletterId('');
                }
            } else {
                $checkout->setNewsletter(false);
            }
            return;
//            dump($result);
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * @param \App\Entity\Checkout
     * @param \App\Entity\Address
     *
     * @return array
     * @throws \Exception
     */
    private function initializeClient($checkout, $address)
    {
        try {
            $userArr = array(
                'username' => $checkout->getUsername(),
                'firstname' => $checkout->getFirstname(),
                'lastname' => $checkout->getLastname(),
                'clientId' => $checkout->getClientId(),
                'newsletterId' => $checkout->getNewsletterId(),
                'newsletter' => $checkout->isNewsletter(),
                'address' => $checkout->getAddress(),
                'city' => $checkout->getCity(),
                'zip' => $checkout->getZip(),
                'district' => $checkout->getDistrict(),
                'phone01' => $checkout->getPhone01(),
                'email' => $checkout->getEmail(),
            );
            $addressArr = array(
                'addresses' => array(
                    'id' => $address->getId(),
                    'name' => $address->getName(),
                    'address' => $checkout->getAddress(),
                    'city' => $checkout->getCity(),
                    'zip' => $checkout->getZip(),
                    'district' => $checkout->getDistrict(),
                )
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

    public function initializeOrder($checkout)
    {
        $this->session->set('address', $checkout->getAddress());
        $this->session->set('comments', $checkout->getComments());
    }
}