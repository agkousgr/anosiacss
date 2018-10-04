<?php


namespace App\Service;

use App\Entity\IssueNewTicket;

class PireausRedirection
{
    /**
     * @var array
     */
    private $bank_config;

    public function __construct()
    {
        $this->bank_config['AcquirerId'] = 14;
        $this->bank_config['MerchantId'] = 2137477493;
        $this->bank_config['PosId'] = 2141384532;
        $this->bank_config['User'] = 'AN895032';
        $this->bank_config['Password'] = 'YC4589964';

    }

    public function submitOrderToPireaus($checkout)
    {
        $wsdl_uri = 'https://paycenter.piraeusbank.gr/services/tickets/issuer.asmx?WSDL';
        $arrContextOptions = array(
            "ssl"=>array(
                "verify_peer" => false,
                "verify_peer_name" => false,
                "allow_self_signed" => true,
            )
        );
        try {
            $ticket_issuer = new \SoapClient($wsdl_uri, array(
                "exceptions" => 1,
                "soap_version" => SOAP_1_2,
                "stream_context" => stream_context_create($arrContextOptions),
            ));
            $params = array(
                'AcquirerId' => $this->bank_config['AcquirerId'],
                'MerchantId' => $this->bank_config['MerchantId'],
                'PosId' => $this->bank_config['PosId'],
                'Username' => $this->bank_config['User'],
                'Password' => hash('md5', $this->bank_config['Password']),
                'RequestType' => '02',
                'CurrencyCode' => 978,
                'MerchantReference' => $checkout->getOrderNo(),
                'Amount' => $checkout->getTotalOrderCost(),
                'Installments' => pack('H', $checkout->getInstallments()),
                'ExpirePreauth' => 0x1e,
                'Bnpl' => 0x00,
                'Parameters' => 'String_for_our_use'
            );
            $response = $ticket_issuer->IssueNewTicket(array('Request' => $params));
            dump($response);
            return;
        } catch (SoapFault $sf) {
            return $sf;
        }


//        $object = new \stdClass();
//        foreach ($params as $key => $value)
//        {
//            $object->$key = $value;
//        }
//
//        $client = new \SoapClient('https://paycenter.piraeusbank.gr/services/tickets/issuer.asmx?WSDL');
//        dump($client->__getFunctions());
//
//        $result = $client->IssueNewTicket(array('Request' => $params));
//        dump($result);
//        return;

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