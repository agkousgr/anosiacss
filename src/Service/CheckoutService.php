<?php

namespace App\Service;

use App\Entity\{Checkout, Cart};
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Environment;

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
     * @var Checkout
     */
    private $curOrder;

    /**
     * @var \SoapClient
     */
    private $client;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * UserAccountService constructor.
     * @param LoggerInterface $logger
     * @param SessionInterface $session
     */
    public function __construct(LoggerInterface $logger, SessionInterface $session, \Swift_Mailer $mailer, Environment $twig)
    {
        $this->logger = $logger;
        $this->session = $session;
        $this->authId = $session->get("authID");
        $this->username = $session->get('anosiaUser');
        $this->curOrder = $session->get('curOrder');
        $this->client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);
        $this->mailer = $mailer;
        $this->twig = $twig;
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
        $this->getAddress($checkout->getClientId(), $checkout);
        $this->getNewsletter($checkout);
        $checkout->setNextPage(1);
        $checkout->setSeries('7021');
        $checkout->setShippingType('1000');
        $checkout->setPaymentType('1000');
        $checkout->setComments('');
        $checkout->setAgreeTerms(false);
        return;
//        return $this->initializeClient($checkout, $address);
    }

    /**
     * @param \App\Entity\Checkout
     * @param \App\Entity\Address
     *
     * @throws \Exception
     */
    public function getUser($checkout, $address = null) // remove nulls in production
    {

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
            $result = $this->client->SendMessage(['Message' => $message]);
            $userResponse = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            if ($userResponse === false) {
                return;
            }
            $userXML = $userResponse->GetDataRows->GetUsersRow;
            if (null !== $address) {
                $address->setClient($userXML->ClientID);
            } else {
                (null !== $userXML->Username) ? $checkout->setUsername($userXML->Username) : $checkout->setUsername('');
                (null !== $userXML->Password) ? $checkout->setPassword($userXML->Password) : $checkout->setPassword('');
                (null !== $userXML->ClientID) ? $checkout->setClientId($userXML->ClientID) : $checkout->setClientId('');
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
            $result = $this->client->SendMessage(['Message' => $message]);
            $clientResponse = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            if ($clientResponse === false) {
                return;
            }
            $this->session->remove('curOrder');
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
     * @param \App\Entity\Address
     *
     * @return string
     */
    public function getAddress($clientId, $address)
    {

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
            $result = $this->client->SendMessage(['Message' => $message]);
            $addressData = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            $addressXML = $addressData->GetDataRows->GetShipAddressRow;
            $address->setClient((int)$clientId);
            (null !== $addressXML->ID) ? $address->setId((int)$addressXML->ID) : $address->setId(0);
            (null !== $addressXML->Name) ? $address->setName((string)$addressXML->Name) : $address->setName('');
            (null !== $addressXML->Address) ? $address->setAddress((string)$addressXML->Address) : $address->setAddress('');
            (null !== $addressXML->Zip) ? $address->setZip((string)$addressXML->Zip) : $address->setZip('');
            (null !== $addressXML->City) ? $address->setCity((string)$addressXML->City) : $address->setCity('');
            (null !== $addressXML->District) ? $address->setDistrict((string)$addressXML->District) : $address->setDistrict('');
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
            $result = $this->client->SendMessage(['Message' => $message]);
            $newsletterData = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            dump($result);
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
            $result = $this->client->SendMessage(['Message' => $message]);
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


    public function submitOrder($checkout, $cartItems)
    {
        $expenses = $this->initializeExpenses($checkout);
        $items = $this->initializeCartItems($cartItems);
        $series = $checkout->getSeries();
        $orderNo = $this->getOrderNo();
        $clientId = $checkout->getClientId();
        $comments = $checkout->getComments();
        $shippingType = $checkout->getShippingType();
        $address = $checkout->getAddress();
        $zip = $checkout->getZip();
        $district = $checkout->getDistrict();
        $city = $checkout->getCity();
        $email = $checkout->getEmail();

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientSetOrderRequest>
    <Type>1024</Type>
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    $expenses
    $items
    <AuthID>$this->authId</AuthID>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
    <Key></Key>
    <Series>$series</Series>
    <Number></Number>
    <EshopNumber>$orderNo</EshopNumber>
    <Status>100</Status>
    <CustomerID>$clientId</CustomerID>
    <Remarks>$comments</Remarks>
    <ShipmentTypeID>$shippingType</ShipmentTypeID>
    <ShipAddress>$address</ShipAddress>
    <ShipZip>$zip</ShipZip>
    <ShipDistrict>$district</ShipDistrict>
    <ShipCity>$city</ShipCity>
    <ShipCarrier>1</ShipCarrier>
</ClientSetOrderRequest>
EOF;
        try {
            $result = $this->client->SendMessage(['Message' => $message]);
            $orderResult = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            dump($message, $result);
            if ((string)$orderResult->IsValid === 'false') {
                $checkout->setOrderNo($orderNo);
                return true;
            }
            return false;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * @param Checkout
     * @return string
     */
    private function initializeExpenses($checkout)
    {
        $expenses = "<Expenses>\n";
        if ($checkout->getShippingType() === '1000') {
//            $expenses .= Write code to get value from Soft1
            $expenses .= "        <ClientSetOrderExpense>
            <ExpenseID>1000</ExpenseID>
            <Value>2.00</Value>
        </ClientSetOrderExpense>
";
        }
        if ($checkout->getPaymentType() === '1003') {
//            $expenses .= Write code to get value from Soft1
            $expenses .= "        <ClientSetOrderExpense>
            <ExpenseID>1003</ExpenseID>
            <Value>1.50</Value>
        </ClientSetOrderExpense>
";
        }
        $expenses .= "    </Expenses>";

        return $expenses;
    }

    /**
     * @param $cartItems
     * @return string
     */
    private function initializeCartItems($cartItems)
    {
        $items = "<Items>\n";
        $count = 1;
        foreach ($cartItems as $cartItem) {
            $id = $cartItem['id'];
            $quantity = $cartItem['quantity'];
            $webPrice = $cartItem['webPrice'];
            $items .= "        <ClientSetOrderItem>
            <Number>$count</Number>
            <ItemID>$id</ItemID>
            <Quantity>$quantity</Quantity>
            <Price>$webPrice</Price>
        </ClientSetOrderItem>
";
            $count++;
        }
        $items .= "    </Items>";
        return $items;
    }

    public function sendOrderConfirmationEmail($checkout)
    {
        $message = (new \Swift_Message('Anosiapharmacy - Νέα παραγγελία #' . $checkout->getOrderNo()))
            ->setFrom('demo@democloudon.cloud')
            ->setTo($checkout->getEmail())
            ->setBody(
                $this->twig->render(
                    'email_templates/order_completed.html.twig'
//                    array('name' => $name)
                ),
                'text/html'
            )/*
             * If you also want to include a plaintext version of the message
            ->addPart(
                $this->renderView(
                    'emails/registration.txt.twig',
                    array('name' => $name)
                ),
                'text/plain'
            )
            */
        ;

        $this->mailer->send($message);
        return;
    }

    /**
     * @return int
     */
    private function getOrderNo()
    {
        $fileSystem = new Filesystem();
        $fileExists = $fileSystem->exists('../uploads/orders_counter');
        if ($fileExists) {
            $orderNo = (int)(file_get_contents('../uploads/orders_counter'));
            $orderNo++;
            $fileSystem->dumpFile('../uploads/orders_counter', $orderNo);
        } else {
            $orderNo = time();
        }
        return $orderNo;
    }

    /**
     * @param $cartItems
     * @param EntityManagerInterface
     */
    public function emptyCart($cartItems, $em)
    {
        foreach ($cartItems as $item) {
            $cartItem = $em->getRepository(Cart::class)->find($item["cartId"]);
            $em->remove($cartItem);
            $em->flush();
        }
        return;
    }
}