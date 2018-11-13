<?php


namespace App\Controller;


use App\Entity\MigrationProducts;
use Doctrine\ORM\EntityManagerInterface;

class MigrateController extends MainController
{
    public function updateS1()
    {
        $client = new \SoapClient('https://caron.cloudsystems.gr/FOeshopWS/ForeignOffice.FOeshop.API.FOeshopSvc.svc?singleWsdl', ['trace' => true, 'exceptions' => true,]);

        $authId = $this->session->get("authID");

        $body = '&lt;Body&gt;&lt;h1&gt;Ingredients&lt;/h1&gt;&lt;/body&gt;';

        $message = <<<EOF
<?xml version="1.0" encoding="utf-16"?>
<ClientSetItemSEORequest>
    <Type>1060</Type>
    <Kind>1</Kind>
    <Domain>pharmacyone</Domain>
    <AuthID>$authId</AuthID>
    <AppID>157</AppID>
    <CompanyID>1000</CompanyID>
    <ItemID>29246</ItemID>
    <Slug>slug</Slug>
    <SEOTitle>SEO Title</SEOTitle>
    <SEODescription>SEO Description</SEODescription>
    <SEOKeywords>SEO Keywords</SEOKeywords>
    <OldSlug>old slug</OldSlug>
    <Ingredients>$body</Ingredients>
    <Instructions>$body</Instructions>
    <MakeID></MakeID>
    <CategoryIDs></CategoryIDs>
    <Summary>$body</Summary>
    <ManufacturID>1001</ManufacturID>
</ClientSetItemSEORequest>
EOF;
        try {
            $itemsArr = array();
            $result = $client->SendMessage(['Message' => $message]);
            $items = simplexml_load_string(str_replace("utf-16", "utf-8", $result->SendMessageResult));
            dump($message, $result);

            return;
        } catch (\SoapFault $sf) {
            echo $sf->faultstring;
        }
    }

    public function migrateImages(EntityManagerInterface $em)
    {
//       Save Path: FOSO/[Serial]/[CompanyID]/[TableName]/[sodtype]/[sosource]/[TableID]
//      [Serial] ο αριθμός της εγκατάστασης του SoftOne
//      [CompanyID] το ID της εταιρίας στο οποίο έχουμε κάνει login
//      [TableName] το όνομα του πίνακα (για τα είδη ‘mtrl’)
//      [sodtype] η τιμή του sodtype αν υπάρχει, διαφορετικά ‘-’  (για τα είδη 51)
//      [sosource] η τιμή του sosource αν υπάρχει, διαφορετικά ‘-’ (για τα είδη δεν υπάρχει)
//      [TableID] το ID του είδους
        try {
            $products = $em->getRepository(MigrationProducts::class)->findBy([], [], 1500, 11500);
            foreach ($products as $product) {
                if ($product->getImages()) {
                    $imagesArr = explode('|', $product->getImages());
                    foreach ($imagesArr as $image) {
                        $file_headers = get_headers($image);
                        if (strpos($file_headers[0], '404') === false) {
                            $imageName = explode('/', $image);
                            if (!is_dir('/home/john/Downloads/anosia-images/FOSO/Serial/1001/mtrl/51/-/' . $product->getS1id())) {
                                mkdir('/home/john/Downloads/anosia-images/FOSO/Serial/1001/mtrl/51/-/' . $product->getS1id());
                            }
                            $img = '/home/john/Downloads/anosia-images/FOSO/Serial/1001/mtrl/51/-/' . $product->getS1id() . '/' . end($imageName);
                            file_put_contents($img, file_get_contents($image));

                        }
                    }
                }

//            $url =
            }
//        $url = 'http://example.com/image.php';
//        $img = '/home/john/Downloads//flower.gif';
//        file_put_contents($img, file_get_contents($url));
            return;
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            dump($product->getS1id());
        }
    }
}