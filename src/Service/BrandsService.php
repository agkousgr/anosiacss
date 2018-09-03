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

    /**
     * @param $makeId
     * @return \SimpleXMLElement
     * @throws \Exception
     */
    public function getBrands($makeId)
    {
        $client = new \SoapClient('https://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetMakeRequest>
    <Type>1032</Type>
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
    <pagesize>1000</pagesize>
    <pagenumber>0</pagenumber>
    <MakeID>$makeId</MakeID>
</ClientGetMakeRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $resultXML = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            $brands = $this->initializeBrands($resultXML->GetDataRows->GetCategoriesRow);
            dump($message, $resultXML);
            return $brands;

        } catch (\SoapFault $sf) {
            throw $sf->faultstring;
        }
    }

    /**
     * @param $brands
     * @return array
     * @throws \Exception
     */
    private function initializeBrands($brands)
    {
        try {
//            if ((string)$pr->WebVisible !== 'false') {
                $brandsArr = array(
                    'id' => $brands->ID,
                    'name' => $brands->Name,
                    'slug' => $brands->Slug,
                    'hasMainImage' => $brands->HasMainPhoto,
                    'imageUrl' => ($brands->HasMainPhoto) ? 'https://caron.cloudsystems.gr/FOeshopAPIWeb/DF.aspx?' . str_replace('[Serial]', '01102459200617', str_replace('&amp;', '&', $brands->MainPhotoUrl)) : ''
                );
//            }
//            'manufacturer' => $pr->ManufactorName
//            return new Response(dump(print_r($this->prCategories)));
            return $brandsArr;
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

}
