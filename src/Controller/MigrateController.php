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
            $data = [];
            $errorData = [];
            $existingImages = [];
            $damagedFiles = [];
            $imageCounter = 0;
            $counter = 0;
            $products = $em->getRepository(MigrationProducts::class)->findBy([], ['id' => 'ASC'], 14000, 0);
//            dump($products);
            foreach ($products as $product) {
                if ($product->getImages()) {
                    $counter++;
//                    dump($product->getSku());
                    $s1ProductData = $productService->getItems('null', $keyword = 'null', 1, $sortBy = 'PriceDesc', $isSkroutz = -1, $makeId = 'null', $priceRange = 'null', $product->getSku());
//                    dump($s1ProductData);
                    if (isset($s1ProductData['id'])) {
                        $imagesArr = explode('|', $product->getImages());
                        dump($s1ProductData['id']);
                        $isMain = 'true';
                        $prImages = $productService->getItemPhotos($s1ProductData['id']);
                        foreach ($imagesArr as $image) {
                            $prId = (isset($s1ProductData['id'])) ? $s1ProductData['id'] : 'error Id on productCode: ' . $product->getSku();

//                            dump($image);
                            $imageName = explode('/', $image);
                            if (!$prImages) {
                                // SAVE IMAGES TO S1
                                $result = $migrationService->saveImage(end($imageName), $isMain, $s1ProductData['id']);
                                if ($result === false) {
                                    $pr = $em->getRepository(MigrationProducts::class)->find($product->getId());
                                    $pr->setImageUpdateError('1');
                                    $em->persist($pr);
                                    $em->flush();
                                }else{
                                    $imageCounter++;
                                }
                            } else {
                                $imageNotExists = true;
                                foreach ($prImages['extraImages'] as $s1image) {
                                    $imageFilename = explode('=', $s1image['imageUrl']);
                                    if (end($imageName) !== end($imageFilename)) {
                                        $imageNotExists = false;
                                    }
                                }
                                if (true === $imageNotExists) {
                                    $result = $migrationService->saveImage(end($imageName), $isMain, $s1ProductData['id']);
                                    if ($result === false) {
                                        $pr = $em->getRepository(MigrationProducts::class)->find($product->getId());
                                        $pr->setImageUpdateError('1');
                                        $em->persist($pr);
                                        $em->flush();
                                    }else{
                                        $imageCounter++;
                                    }

                                } else {
                                    $existingImages[] = [
                                        'imageName' => end($imageName),
                                        'isMain' => $isMain,
                                        'prName' => $product->getName(),
                                        'id' => $prId
                                    ];
                                }
                            }
//                        dump(end($imageName), $isMain, $product->getS1id());
                            $data[] = [
                                'imageName' => end($imageName),
                                'isMain' => $isMain,
                                'prName' => $product->getName(),
                                'id' => $prId
                            ];
                            // SAVE IMAGES LOCALHOST
                            $file_headers = get_headers($image);
                            if (strpos($file_headers[0], '404') === false) {
                                $imageName = explode('/', $image);
                                if (!is_dir('/home/john/Downloads/anosia-images/' . $s1ProductData['id'])) {
                                    mkdir('/home/john/Downloads/anosia-images/' . $s1ProductData['id']);
                                }
                                $img = '/home/john/Downloads/anosia-images/' . $s1ProductData['id'] . '/' . end($imageName);
                                file_put_contents($img, file_get_contents($image));

                            }else{
                                $damagedFiles[] = [
                                    'imageName' => end($imageName),
                                    'isMain' => $isMain,
                                    'prName' => $product->getName(),
                                    'id' => $prId
                                ];
                            }
                            $isMain = 'false';
                        }
                    } else {
                        $errorData[] = [
                            'prName' => $product->getName(),
                            'prCode' => $product->getSku()
                        ];
                    }
                } else {
                    $data[] = [
                        'imageName' => '',
                        'isMain' => '',
                        'prName' => $product->getName(),
                        'id' => ''
                    ];
                }
            }
            return $this->render('migrations/images.html.twig', [
                'data' => $data,
                'counter' => $counter,
                'errorData' => $errorData,
                'existingImages' => $existingImages,
                'damagedFiles' => $damagedFiles,
                'imageCounter' => $imageCounter
            ]);

        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}