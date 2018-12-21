<?php


namespace App\Controller;


use App\Entity\AnosiaSearchKeywords;
use App\Entity\Category;
use App\Entity\MigrationProducts;
use App\Entity\TempImages;
use App\Service\MigrationService;
use App\Service\ProductService;
use Doctrine\ORM\EntityManagerInterface;

class MigrateController extends MainController
{
    public function updateMigratedProductsWithS1Id(EntityManagerInterface $em, MigrationService $migrationService, ProductService $productService)
    {
        $products = $em->getRepository(MigrationProducts::class)->findBy([], [], 2500, 11000);
        foreach ($products as $pr) {
            $s1ProductData = $productService->getItems('null', $keyword = 'null', 1, $sortBy = 'PriceDesc', $isSkroutz = -1, $makeId = 'null', $priceRange = 'null', $pr->getSku());

            if ($s1ProductData) {
                $pr->setS1id(intval($s1ProductData['id']));
                $em->persist($pr);
                $em->flush();
            }
        }
        return;
    }

    public function updateS1(EntityManagerInterface $em, MigrationService $migrationService, ProductService $productService)
    {
        $products = $em->getRepository(MigrationProducts::class)->findBy([], [], 3000, 4000);
//        $products = $em->getRepository(MigrationProducts::class)->findBy(['sku' => '015124']);
//        dump($products);
        foreach ($products as $pr) {
//            $s1ProductData = $productService->getItems($pr->getS1id(), $keyword = 'null', 1, $sortBy = 'PriceDesc', $isSkroutz = -1, $makeId = 'null', $priceRange = 'null', 'null');
            $s1ProductData = $productService->getItems('null', $keyword = 'null', 1, $sortBy = 'PriceDesc', $isSkroutz = -1, $makeId = 'null', $priceRange = 'null', $pr->getSku());
            dump($s1ProductData);

            if ($s1ProductData) {
                $migrationService->updateProducts($pr->getS1id(), $pr->getOldId(), $pr->getSlug(), $pr->getOldSlug(), $pr->getSeoTitle(), '', $pr->getSeoKeywords(), $pr->getIngredients(), $pr->getInstructions(), $pr->getSmallDescription(), $pr->getLargeDescription(), $s1ProductData['manufacturerId'], $pr->getCategoryIds());
                $pr->setManufacturerId(intval($s1ProductData['manufacturerId']));
                $webVisible = (strval($s1ProductData['isVisible']) === 'true') ? '1' : '0';
                $pr->setWebVisible($webVisible);
                $em->persist($pr);
                $em->flush();
            }
        }
        return;
    }

    public function updateProductsFromS1(EntityManagerInterface $em, MigrationService $migrationService, ProductService $productService)
    {
        $nullMnf = '';
        $prevName = '';
        $duplicates = '';
        $s1ProductData = $productService->getItems('null', $keyword = 'null', 15000, $sortBy = 'NameAsc', $isSkroutz = -1, $makeId = 'null', $priceRange = 'null', 'null', 1, 'null', 0);
        foreach ($s1ProductData as $s1pr) {
//            dump($s1pr);
//continue;
            if ($prevName === strval($s1pr['name'])) {
                $duplicates .= strval($s1pr['id']) . ',';
                dump($duplicates);
                die();
                continue;
            } else {
                $prevName = strval($s1pr['name']);
            }
            dump($s1pr);
            die();
            $pr = $em->getRepository(MigrationProducts::class)->findOneBy(['s1id' => $s1pr['id']]);
//            dump($pr->getWebPrice());
            if ($pr) {
                if ($pr->getWebPrice() > 0) {
                    continue;
                }
                $webVisible = (strval($s1pr['isVisible']) === 'true') ? '1' : '0';
//                if ($pr->getWebVisible() !== '1')
//                    continue;
                $manufacturerId = (intval($s1pr['manufacturerId']) > 0) ? intval($s1pr['manufacturerId']) : null;
                if ($manufacturerId === null) {
                    $nullMnf .= $pr->getS1id() . ',';
                }
                $pr->setWebVisible($webVisible);
                $pr->setManufacturerId($manufacturerId);
                $pr->setRetailPrice(strval($s1pr['retailPrice']));
                $pr->setWebPrice(strval($s1pr['webPrice']));
                $pr->setBarcode(strval($s1pr['mainBarcode']));
                $pr->setDiscount(strval($s1pr['discount']));
                $em->persist($pr);
                $em->flush();
//                dump($pr);
            }
        }
        dump($nullMnf, $duplicates);
        return;
    }

    public function getProductsWithNoImage(EntityManagerInterface $em, MigrationService $migrationService, ProductService $productService)
    {
        $products = $em->getRepository(MigrationProducts::class)->findBy([], [], 10, 0);
        foreach ($products as $pr) {

        }

        $s1ProductData = $productService->getItems('null', $keyword = 'null', 100, $sortBy = 'PriceDesc', $isSkroutz = -1, $makeId = 'null', $priceRange = 'null', 'null', 1);
        return;
    }

    public function getProductsWithNoImageFromS1(EntityManagerInterface $em, MigrationService $migrationService, ProductService $productService)
    {
        $noExistingProducts = [];
        $counter = 0;
        $s1ProductData = $productService->getItems('null', $keyword = 'null', 13500, $sortBy = 'PriceDesc', $isSkroutz = -1, $makeId = 'null', $priceRange = 'null', 'null', 1);
//            dump($s1ProductData);
        $reversedPr = array_reverse($s1ProductData);
        foreach ($reversedPr as $s1pr) {
            $counter++;
            if (strval($s1pr['hasMainImage']) === 'false') {
                $pr = $em->getRepository(MigrationProducts::class)->findOneBy(['s1id' => intval($s1pr['id'])]);
//                dump($s1pr, $pr);
                if ($pr) {
                    $pr->setImageUpdateError('1');
                    $em->persist($pr);
                    $em->flush();
                } else {
//                    if ($counter < 6000) {
                    $noExistingProducts[] = $s1pr['prCode'];
//                    }
                }
            }
            if ($counter === 6001) {
                dump($noExistingProducts);
                return;
            }
        }
        return;
    }

    public function updateFromTempImages(EntityManagerInterface $em)
    {
        $products = $em->getRepository(MigrationProducts::class)->findBy(['images' => null], ['id' => 'DESC'], 6500, 2000);

        foreach ($products as $pr) {
//            dump($pr);
            $item = $em->getRepository(TempImages::class)->findBy(['s1id' => $pr->getS1id()]);
            if ($item) {
                $pr->setImages($item[0]->getImages());
                $pr->setImageUpdateError(1);
                $em->persist($pr);
                $em->flush();
                dump($item);
            }
        }
//        dump($products);
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
            $products = $em->getRepository(MigrationProducts::class)->findBy(['imageUpdateError' => '1'], ['id' => 'ASC'], 1000, 0);
//            dump($products);
//            die();
            foreach ($products as $product) {
                if ($product->getImages()) {
                    $counter++;
                    dump($product);
//                    $s1ProductData = $productService->getItems('null', $keyword = 'null', 1, $sortBy = 'PriceDesc', $isSkroutz = -1, $makeId = 'null', $priceRange = 'null', $product->getSku());
                    if ($product->getS1id()) {
                        $imagesArr = explode('|', $product->getImages());
//                        dump($s1ProductData['id']);
                        $isMain = 'true';
                        $prImages = $productService->getItemPhotos($product->getS1id());
                        foreach ($imagesArr as $image) {
                            $prId = (isset($s1ProductData['id'])) ? $product->getS1id() : 'error Id on productCode: ' . $product->getSku();

//                            dump($image);
                            $imageName = explode('/', $image);
                            if (!$prImages) {
                                // SAVE IMAGES TO S1
                                $result = $migrationService->saveImage(str_replace('&', '-', end($imageName)), $isMain, $product->getS1id());
                                if ($result === false) {
                                    $pr = $em->getRepository(MigrationProducts::class)->find($product->getId());
                                    $pr->setImageUpdateError('1');
                                    $em->persist($pr);
                                    $em->flush();
                                } else {
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
                                    $result = $migrationService->saveImage(end($imageName), $isMain, $product->getS1id());
                                    if ($result === false) {
                                        $pr = $em->getRepository(MigrationProducts::class)->find($product->getId());
                                        $pr->setImageUpdateError('1');
                                        $em->persist($pr);
                                        $em->flush();
                                    } else {
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
                                if (!is_dir('/home/john/Downloads/anosia-images/' . $product->getS1id())) {
                                    mkdir('/home/john/Downloads/anosia-images/' . $product->getS1id());
                                }
                                $img = '/home/john/Downloads/anosia-images/' . $product->getS1id() . '/' . str_replace('&', '-', end($imageName));
                                file_put_contents($img, file_get_contents($image));

                            } else {
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
                        dump($errorData);
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

    public function updateAnosiaKeywordsSlug(EntityManagerInterface $em)
    {
        $categories = $em->getRepository(AnosiaSearchKeywords::class)->findAll();
        foreach ($categories as $category) {
            if (!$category->getSlug()) {
                $s1ctg = $em->getRepository(Category::class)->findOneBy(['s1id' => $category->getCategoryId()]);
                if ($s1ctg && $s1ctg->getSlug()) {
                    $category->setSlug($s1ctg->getSlug());
                    $em->persist($category);
                    $em->flush();
                }
            }
        }
        return;
    }

}