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
     * @param $slug
     *
     * @return \SimpleXMLElement
     * @throws \Exception
     */
    public function getBrands($slug)
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
    <MakeID>-1</MakeID>
    <Slug>$slug</Slug>
</ClientGetMakeRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $resultXML = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            dump($message, $result);
            $brands = $this->initializeBrands($resultXML->GetDataRows->GetMakeRow);
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
            $brandsArr = [];
            foreach ($brands as $brand) {
                $brandsArr[] = array(
                    'id' => $brand->ID,
                    'name' => $brand->Name,
                    'slug' => $brand->Slug,
                    'hasMainImage' => $brand->HasMainPhoto,
                    'imageUrl' => ($brand->HasMainPhoto) ? 'https://caron.cloudsystems.gr/FOeshopAPIWeb/DF.aspx?' . str_replace('[Serial]', '01102459200617', str_replace('&amp;', '&', $brand->MainPhotoUrl)) : ''
                );
//            }
            }
            dump($brandsArr);
            return $brandsArr;
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

}
