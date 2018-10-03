<?php


namespace App\Service;


class PireausRedirection
{
    public function submitOrderToPireaus($checkout)
    {

        $AcquirerId = 14;
        $MerchantID = 2137477493;
        $PosID = 2141384532;
        $UserName = 'AN895032';
        $Password = md5('YC4589964');
        $RequestType = '00';
        $CurrencyCode = 978;
        $MerchantReference = $checkout->getOrderNo();
        $Amount = $checkout->getTotalOrderCost();
        $Installments = $checkout->getInstallments();
        // Αφορά συναλλαγές προέγκρισης (RequestType
        //=«00») και περιέχει το πλήθος των ημερών
        //εντός των οποίων η προέγκριση μπορεί να
        //ολοκληρωθεί. Μέγιστη τιμή: 30 ημέρες min 1
        $ExpirePreauth = 1;
        $Bnpl = 0;
        $Parameters = 'String_for_our_use'; // Spaces are NOT allowed

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'http://testcURL.com',
            CURLOPT_USERAGENT => 'Codular Sample cURL Request',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => array(
                item1 => 'value',
                item2 => 'value2'
            )
        ));
        $resp = curl_exec($curl);
        curl_close($curl);
    }
}