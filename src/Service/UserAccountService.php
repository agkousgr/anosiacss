<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 3/6/2018
 * Time: 3:43 μμ
 */

namespace App\Service;


class UserAccountService
{
    public function userAlreadyExists($username, $authId)
    {
        $client = new \SoapClient('http://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetUsersRequest>
    <Type>1015</Type>
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <AuthID>$authId</AuthID>
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
//            return $result->SendMessageResult;
//            return str_replace("utf-16", "utf-8", $result->SendMessageResult);
            $userData = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            dump($result);
            if ((int)$userData->RowsCount === 0) {
                return null;
            } else {
                return $userData->GetDataRows->GetUsersRow;
            }
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }

    }

    public function createUser($userData, $authId)
    {
        dump($userData);
        return 0;
    }
}