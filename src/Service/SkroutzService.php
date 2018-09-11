<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 5/9/2018
 * Time: 7:40 μμ
 */

namespace App\Service;


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
        $products = $this->getItems('null', 'null', '10000', 'NameAsc', '1' );
        dump($products);
        $initializedProducts = $this->initializeProducts($products);
        $xmlOutput = $this->createXml($initializedProducts);
        return $xmlOutput;
    }

    private function createXml($products)
    {
        $counter = 0;
//        $xml_output = '';
        $xml_output = '<?xml version="1.0" encoding="UTF-8"?>    
        <volleyplus>
            <created_at>' . date('Y-m-d H:i') . '</created_at>
                <products>';



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
                        'summary' => $pr->SmallDescriptionHTML,
                        'body' => $pr->LargeDescriptionHTML,
                        'extraInfo' => $pr->InstructionsHTML,
                        'isVisible' => $pr->WebVisible,
                        'retailPrice' => $pr->RetailPrice,
                        'discount' => $pr->WebDiscountPerc,
                        'webPrice' => $pr->WebPrice,
                        'outOfStock' => $pr->OutOfStock,
                        'remainNotReserved' => $pr->Remain,
                        'webFree' => $pr->WebFree,
                        'overAvailability' => $pr->OverAvailability,
                        'avail1to3' => $pr->AvailIn1To3Days,
                        'maxByOrder' => $pr->MaxByOrder,
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
}