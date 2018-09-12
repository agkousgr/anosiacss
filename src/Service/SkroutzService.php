<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 5/9/2018
 * Time: 7:40 μμ
 */

namespace App\Service;


use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SkroutzService
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var string
     * @param SessionInterface $session
     */
    private $authId;

    public function __construct(LoggerInterface $logger, SessionInterface $session)
    {
        $this->logger = $logger;
        $this->session = $session;
        $this->authId = $session->get("authID");
//        dump($this->authId);
    }

    public function initilizeSkroutzXml()
    {
        $products = $this->getItems('null', 'null', '10000', 'NameAsc', '1');
        dump($products);
        $xmlOutput = $this->createXml($products);
        return $xmlOutput;
    }

    private function createXml($products)
    {
        $counter = 0;
//        $xml_output = '';
        $name_color = '';
        $title_category = '';
        $product_category = '';
        $sizes = '';
        $color = '';
        $xml_output = '<?xml version="1.0" encoding="UTF-8"?>    
        <anosiapharmacy>
            <created_at>' . date('Y-m-d H:i') . '</created_at>
                <products>';
        foreach ($products as $product) {

            $xml_output .= '
    <product>
        <id>' . $product['id'] . '</id>
        <name><![CDATA[' . $title_category . ' ' . $product["name"] . $name_color . ']]></name>
        <link><![CDATA[https://www.anosiapharmacy.gr/product/' . $product["slug"] . ']]></link>
        <image><![CDATA[' . $product["imageUrl"] . ']]></image>
        <category><![CDATA[' . $product_category . ']]></category>
        <price_with_vat>' . $product["webPrice"] . '</price_with_vat>
        <manufacturer><![CDATA[' . $product["brand"] . ']]></manufacturer>
        <mpn>' . $product["mainBarcode"] . '</mpn>        
        <availability>' . $product["availability"] . '</availability>
        <size>' . $sizes . '</size>
        <color>' . $color . '</color>
      </product>';
        }


        $xml_output .= '</products>
</volleyplus>';
        return $xml_output;
    }


    public function getItems()
    {
        $client = new \SoapClient('https://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientGetItemsRequest>
    <Type>1005</Type>
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <AuthID>$this->authId</AuthID>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
    <pagesize>20000</pagesize>
    <pagenumber>0</pagenumber>
    <ItemID>null</ItemID>
    <ItemCode>null</ItemCode>
    <MakeID>null</MakeID>
    <IsSkroutz>1</IsSkroutz>
    <SearchToken>null</SearchToken>
    <OrderBy>NameAsc</OrderBy>
</ClientGetItemsRequest>
EOF;
        try {
            $result = $client->SendMessage(['Message' => $message]);
            $items = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            dump($items);
            $itemsArr = $this->initializeProducts($items->GetDataRows->GetItemsRow);

            return $itemsArr;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    /**
     * @param $products
     * @return array
     * @throws \Exception
     */
    private function initializeProducts($products)
    {
        try {
            $prArr = array();
            foreach ($products as $pr) {
                if ((string)$pr->WebVisible !== "false") {
                    $prArr[] = array(
                        'id' => $pr->ID,
                        'name' => $pr->Name2,
                        'slug' => $this->slugify($pr->Name2),
                        'summary' => $pr->SmallDescriptionHTML,
                        'body' => $pr->LargeDescriptionHTML,
                        'extraInfo' => $pr->InstructionsHTML,
                        'isVisible' => $pr->WebVisible,
                        'retailPrice' => $pr->RetailPrice,
                        'discount' => $pr->WebDiscountPerc,
                        'webPrice' => $pr->WebPrice,
                        'outOfStock' => $pr->OutOfStock,
                        'brand' => $pr->MakeName,
                        'mainBarcode' => $pr->MainBarcode,
                        'availability' => $this->getAvailability($pr->AvailIn1To3Days),
                        'hasMainImage' => $pr->HasMainPhoto,
                        'imageUrl' => ($pr->HasMainPhoto) ? 'https://caron.cloudsystems.gr/FOeshopAPIWeb/DF.aspx?' . str_replace('[Serial]', '01102459200617', str_replace('&amp;', '&', $pr->MainPhotoUrl)) : ''
                    );
                }
            }
//            dump($prArr);
            return $prArr;
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    private function getAvailability($availability)
    {
        return ($availability === 'true') ? '1-3 days' : 'Pre Order';
    }

    private function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
}