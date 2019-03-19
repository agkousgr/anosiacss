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
     * @var double
     */
    private $cartItemsCost;

    /**
     * UserAccountService constructor.
     * @param LoggerInterface $logger
     * @param SessionInterface $session
     */
    public function __construct(LoggerInterface $logger, SessionInterface $session, \Swift_Mailer $mailer, Environment $twig, $s1Credentials)
    {
        $this->logger = $logger;
        $this->session = $session;
        $this->authId = $session->get("authID");
        $this->kind = $s1Credentials['kind'];
        $this->domain = $s1Credentials['domain'];
        $this->appId = $s1Credentials['appId'];
        $this->companyId = $s1Credentials['companyId'];
        $this->username = $session->get('anosiaUser');
        $this->curOrder = $session->get('curOrder');
        $this->client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->cartItemsCost = 0;
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
//        $this->getAddress($checkout->getClientId(), $checkout);
        $this->getNewsletter($checkout);
        $checkout->setNextPage(1);
        $checkout->setSeries('7023');
//        $checkout->setShippingType('1000');
//        $checkout->setPaymentType('1000');
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
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
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
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
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
            $userXML = $clientResponse->GetDataRows->GetClientsRow;
            list($firstname, $lastname) = explode(' ', $userXML->NAME . ' ');
            $checkout->setFirstname($firstname);
            $checkout->setLastname($lastname);
            $checkout->setEmail($userXML->EMAIL);
            (null !== $userXML->ADDRESS) ? $checkout->setAddress((string)$userXML->ADDRESS) : $checkout->setAddress('');
            (null !== $userXML->ZIP) ? $checkout->setZip((string)$userXML->ZIP) : $checkout->setZip('');
            (null !== $userXML->CITY) ? $checkout->setCity((string)$userXML->CITY) : $checkout->setCity('');
            (null !== $userXML->DISTRICT) ? $checkout->setDistrict((string)$userXML->DISTRICT) : $checkout->setDistrict('');
            (null !== $userXML->PHONE01) ? $checkout->setPhone01((string)$userXML->PHONE01) : $checkout->setPhone01('');
            (null !== $userXML->AFM) ? $checkout->setAfm((string)$userXML->AFM) : $checkout->setAfm('');
            (null !== $userXML->IRS) ? $checkout->setIrs((string)$userXML->IRS) : $checkout->setIrs('');

            return;
//            return $clientData = $this->initializeClient($userXML->GetDataRows->GetClientsRow);
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * @param \App\Entity\Checkout
     *
     * @return string
     */
    public function getAddress($clientId, $entity, $id = -1)
    {

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
            $result = $this->client->SendMessage(['Message' => $message]);
            $addressData = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            $addressXML = $addressData->GetDataRows->GetShipAddressRow;
            $entity->setClient((int)$clientId);
            (null !== $addressXML->ID) ? $entity->setId((int)$addressXML->ID) : $entity->setId(0);
            (null !== $addressXML->Name) ? $entity->setName((string)$addressXML->Name) : $entity->setName('');
            (null !== $addressXML->Address) ? $entity->setAddress((string)$addressXML->Address) : $entity->setAddress('');
            (null !== $addressXML->Zip) ? $entity->setZip((string)$addressXML->Zip) : $entity->setZip('');
            (null !== $addressXML->City) ? $entity->setCity((string)$addressXML->City) : $entity->setCity('');
            (null !== $addressXML->District) ? $entity->setDistrict((string)$addressXML->District) : $entity->setDistrict('');
            return;
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
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <pagesize>1</pagesize>
    <pagenumber>0</pagenumber>
    <Email>$this->username</Email>
</ClientGetNewsletterRequest>
EOF;
        try {
            $result = $this->client->SendMessage(['Message' => $message]);
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

  /*  public function getClientId($username) // to be deleted in production
    {
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
  */

    /**
     * @param $checkout
     * @param $cartItems
     * @return bool
     */
    public function submitOrder($checkout, $cartItems)
    {
        $expenses = $this->initializeExpenses($checkout);
        $items = $this->initializeCartItems($cartItems);
        if (!$checkout->getSeries())
            $checkout->setSeries('7023');
        $series = $checkout->getSeries();
        $orderNo = $checkout->getOrderNo();
        $clientId = $checkout->getClientId();
        $comments = $checkout->getComments();
        $paymentType = $checkout->getPaymentType();
        $shippingType = $checkout->getShippingType();
        $address = $checkout->getAddress();
        $zip = $checkout->getZip();
        $district = $checkout->getDistrict();
        $city = $checkout->getCity();
        $coupon = $checkout->getCouponId();
        $recepientName = $checkout->getRecepientName();
        $shipKindId = ($shippingType === '104') ? 1005 : 1000;
        $email = $checkout->getEmail();
        $couponDisc = ($checkout->getCouponDisc()) ?: 0;
        $couponDiscPerc = ($checkout->getCouponDiscPerc()) ?: 0;
        // VAT percentage should come from a config value
        $checkout->setTotalOrderCost($this->cartItemsCost + $checkout->getShippingCost() / 1.24 + $checkout->getAntikatavoliCost() / 1.24);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientSetOrderRequest>
    <Type>1024</Type>
    <Kind>$this->kind</Kind>
    <Domain>$this->domain</Domain>
    $expenses
    $items
    <AuthID>$this->authId</AuthID>
    <AppID>$this->appId</AppID>
    <CompanyID>$this->companyId</CompanyID>
    <Key></Key>
    <Series>$series</Series>
    <Number>*</Number>
    <EshopNumber>$orderNo</EshopNumber>
    <Status>100</Status>
    <CustomerID>$clientId</CustomerID>
    <Remarks>$comments</Remarks>
    <PayTypeID>$paymentType</PayTypeID>
    <Comments>$comments</Comments>
    <ShipmentTypeID>$shippingType</ShipmentTypeID>
    <ShipAddress>$address</ShipAddress>
    <ShipZip>$zip</ShipZip>
    <ShipDistrict>$district</ShipDistrict>
    <ShipCity>$city</ShipCity>
    <ShipCarrier>1</ShipCarrier>
    <VoucherID>$coupon</VoucherID>
    <VoucherRecepient>$recepientName</VoucherRecepient>
    <Discount1Perc>$couponDiscPerc</Discount1Perc>
    <Discount2Perc>0</Discount2Perc>
    <Discount1Value>$couponDisc</Discount1Value>
    <ShipkindID>$shipKindId</ShipkindID>
</ClientSetOrderRequest>
EOF;
        try {
//            return true;
            $result = $this->client->SendMessage(['Message' => $message]);
            $orderResult = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            if ((string)$orderResult->IsValid === 'true') {
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
        // Todo: Set expenseId in user's general configuration
        $expenses = "<Expenses>\n";
        if ($checkout->getShippingType() === '104') {
//            $expenses .= Write code to get value from Soft1
            $expenses .= "        <ClientSetOrderExpense>
            <ExpenseID>104</ExpenseID>
            <Value>2.00</Value>
        </ClientSetOrderExpense>
";
        }
        if ($checkout->getPaymentType() === '1007') {
//            $expenses .= Write code to get value from Soft1
            $expenses .= "        <ClientSetOrderExpense>
            <ExpenseID>105</ExpenseID>
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
            $webPrice = floatval($cartItem['retailPrice']);
            $discount = floatval($cartItem['discount']);
            $this->cartItemsCost += number_format(($quantity * $webPrice), 2, '.', ',');
            $items .= "        <ClientSetOrderItem>
            <Number>$count</Number>
            <ItemID>$id</ItemID>
            <Quantity>$quantity</Quantity>
            <Price>$webPrice</Price>
            <Discount1Perc>$discount</Discount1Perc>          
        </ClientSetOrderItem>
";
            $count++;
        }
        $items .= "    </Items>";
        return $items;
    }

    /**
     * @param App\Entity\Checkout
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendOrderConfirmationEmail($checkout, $cartItems)
    {
        $message = (new \Swift_Message('Anosiapharmacy - Νέα παραγγελία #' . $checkout->getOrderNo()))
            ->setFrom('demo@democloudon.cloud')
            ->setTo($checkout->getEmail())
            ->setBody(
                $this->twig->render(
                    'email_templates/order_completed.html.twig', [
                        'checkout' => $checkout,
                        'cartItems' => $cartItems
                    ]
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

 /*   public function getOrders($clientId)
    {
        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetUsersRequest>
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

    } */

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

    public function calculateCartCost($cartItems)
    {
        $cartTotal = 0;
        foreach ($cartItems as $cartItem) {
            $quantity = $cartItem['quantity'];
            $webPrice = floatval($cartItem['webPrice']);
            $cartTotal += number_format(($quantity * $webPrice), 2, '.', ',');
        }
        return $cartTotal;
    }
}