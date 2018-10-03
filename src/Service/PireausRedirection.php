<?php


namespace App\Service;

use App\Entity\IssueNewTicket;

class PireausRedirection
{
    public function submitOrderToPireaus($checkout)
    {
        $IssueNewTicket = new IssueNewTicket();
        $params = [
            'AcquirerId' => 14,
            'MerchantID' => 2137477493,
            'PosID' => 2141384532,
            'UserName' => 'AN895032',
            'Password' => md5('YC4589964'),
            'RequestType' => '00',
            'CurrencyCode' => 978,
            'MerchantReference' => $checkout->getOrderNo(),
            'Amount' => $checkout->getTotalOrderCost(),
            'Installments' => $checkout->getInstallments(),
            // Αφορά συναλλαγές προέγκρισης (RequestType
            //=«00») και περιέχει το πλήθος των ημερών
            //εντός των οποίων η προέγκριση μπορεί να
            //ολοκληρωθεί. Μέγιστη τιμή: 30 ημέρες min 1
            'ExpirePreauth' => 1,
            'Bnpl' => 0,
            'Parameters' => 'String_for_our_use' // Spaces are NOT allowed
        ];
        $IssueNewTicket->setAcquirerId(14);
        $IssueNewTicket->setMerchantID(2137477493);
        $IssueNewTicket->setPosID(2141384532);
        $IssueNewTicket->setUserName('AN895032');
        $IssueNewTicket->setPassword('YC4589964');
        $IssueNewTicket->setCurrencyCode('978');
        $IssueNewTicket->setMerchantReference((string)$checkout->getOrderNo());
        $IssueNewTicket->setAmount($checkout->getTotalOrderCost());
        $IssueNewTicket->setInstallments($checkout->getInstallments());
        $IssueNewTicket->setExpirePreauth(1);
        $IssueNewTicket->setBnpl(0);
        $IssueNewTicket->setParameters('String_for_our_use');

        $client = new \SoapClient('https://paycenter.piraeusbank.gr/services/tickets/issuer.asmx?WSDL');
        dump($client->__getFunctions());

        $result = $client->IssueNewTicket($IssueNewTicket);
        dump($result);
        return;

//        $curl = curl_init();
//        curl_setopt_array($curl, array(
//            CURLOPT_RETURNTRANSFER => 1,
//            CURLOPT_URL => 'http://testcURL.com',
//            CURLOPT_USERAGENT => 'Codular Sample cURL Request',
//            CURLOPT_POST => 1,
//            CURLOPT_POSTFIELDS => array(
//                item1 => 'value',
//                item2 => 'value2'
//            )
//        ));
//        $resp = curl_exec($curl);
//        curl_close($curl);
    }
}