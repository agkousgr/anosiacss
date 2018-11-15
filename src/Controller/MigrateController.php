<?php


namespace App\Controller;


use App\Entity\MigrationProducts;
use App\Service\MigrationService;
use App\Service\ProductService;
use Doctrine\ORM\EntityManagerInterface;

class MigrateController extends MainController
{
    public function updateS1(EntityManagerInterface $em, MigrationService $migrationService, ProductService $productService)
    {
        $products = $em->getRepository(MigrationProducts::class)->findBy([], [], 500, 13500);
//        $products = $em->getRepository(MigrationProducts::class)->findBy(['sku' => '22']);

        foreach ($products as $pr) {
            $s1ProductData = $productService->getItems('null', $keyword = 'null', 1, $sortBy = 'PriceDesc', $isSkroutz = -1, $makeId = 'null', $priceRange = 'null', '22');
            dump($pr->getSku());
//            dump($s1ProductData);
            if ($s1ProductData) {
            $migrationService->updateProducts($s1ProductData['id'], $pr->getSlug(), $pr->getOldSlug(), $pr->getSeoTitle(), '', $pr->getSeoKeywords(), $pr->getIngredients(), $pr->getInstructions(), $pr->getSmallDescription(), $pr->getLargeDescription(), $s1ProductData['manufacturerId']);
            }
        }
        return;
    }

    public function migrateImages(EntityManagerInterface $em, MigrationService $migrationService, ProductService $productService)
    {
//       Save Path: FOSO/[Serial]/[CompanyID]/[TableName]/[sodtype]/[sosource]/[TableID]
//      [Serial] ο αριθμός της εγκατάστασης του SoftOne
//      [CompanyID] το ID της εταιρίας στο οποίο έχουμε κάνει login
//      [TableName] το όνομα του πίνακα (για τα είδη ‘mtrl’)
//      [sodtype] η τιμή του sodtype αν υπάρχει, διαφορετικά ‘-’  (για τα είδη 51)
//      [sosource] η τιμή του sosource αν υπάρχει, διαφορετικά ‘-’ (για τα είδη δεν υπάρχει)
//      [TableID] το ID του είδους


        try {
            $products = $em->getRepository(MigrationProducts::class)->findBy([], [], 1, 0);
            foreach ($products as $product) {
                if ($product->getImages()) {
                    $s1ProductData = $productService->getItems('null', $keyword = 'null', 1, $sortBy = 'PriceDesc', $isSkroutz = -1, $makeId = 'null', $priceRange = 'null', $product->getSku());
                    dump($s1ProductData);
                    $imagesArr = explode('|', $product->getImages());
                    $isMain = 'true';
                    foreach ($imagesArr as $image) {
                        // SAVE IMAGES TO S1
                        $imageName = explode('/', $image);
                        $result = $migrationService->saveImage(end($imageName), $isMain, $s1ProductData['id']);
                        if ($result === false) {
                            $pr = $em->getRepository(MigrationProducts::class)->find($product->getId());
                            $pr->setImageUpdateError('1');
                            $em->persist($pr);
                            $em->flush();
                        }
//                        dump(end($imageName), $isMain, $product->getS1id());
                        $isMain = 'false';

                        // SAVE IMAGES LOCALHOST
                        $file_headers = get_headers($image);
                        if (strpos($file_headers[0], '404') === false) {
                            $imageName = explode('/', $image);
                            if (!is_dir('/home/john/Downloads/anosia-images/FOSO/Serial/1001/mtrl/51/-/' . $s1ProductData['id'])) {
                                mkdir('/home/john/Downloads/anosia-images/FOSO/Serial/1001/mtrl/51/-/' . $s1ProductData['id']);
                            }
                            $img = '/home/john/Downloads/anosia-images/FOSO/Serial/1001/mtrl/51/-/' . $s1ProductData['id'] . '/' . end($imageName);
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
        }
    }
}