<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 5/8/2018
 * Time: 3:41 μμ
 */

namespace App\Service;


use Symfony\Component\HttpFoundation\Session\SessionInterface;

class BrandsService
{
    /**
     * @var string
     * @param SessionInterface $session
     */
    private $authId;

    public function __construct(SessionInterface $session)
    {
        $this->authId = $session->get("authID");
    }

    public function getBrands()
    {
        $client = new \SoapClient('https://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetMakeRequest>
    <Type>1007</Type>
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
    <pagesize>1000</pagesize>
    <pagenumber>0</pagenumber>
    <MakeID>-1</MakeID>
</ClientGetMakeRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $resultXML = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
//            $brands = $this->initializeCategories($resultXML->GetDataRows->GetCategoriesRow);
            dump($resultXML);
            return $resultXML;

        } catch (\SoapFault $sf) {
            throw $sf->faultstring;
        }
    }
}
