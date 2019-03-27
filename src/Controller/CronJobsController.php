<?php

namespace App\Controller;

use App\Entity\{AvailabilityTypes, Product};
use App\Service\{
    CronJobsService, ProductService, SoftoneLogin
};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;

class CronJobsController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var string
     */
    private $authId;

    public function __construct(EntityManagerInterface $em, SoftoneLogin $softoneLogin)
    {
        $this->em = $em;
        $this->authId = $softoneLogin->login();
    }

    public function synchronizeProducts(ProductService $productService, LoggerInterface $logger)
    {
//        $cmd = $this->em->getClassMetadata($pr);
//        $connection = $this->em->getConnection();
//        $connection->beginTransaction();
        $prevName = '';
        $duplicates = [];
        try {
            $count = $productService->getItemsCount('null', 'null', 'null', 1, 'null');
            echo($count);
            die();
            $s1products = $productService->getItems('null', 'null', 10000, 'NameAsc', -1, 'null', 'null', 'null', 1, 'null', 0);
            if ($s1products) {
//                $connection->query('SET FOREIGN_KEY_CHECKS=0');
//                $connection->query('DELETE FROM products');
//                // Beware of ALTER TABLE here--it's another DDL statement and will cause
//                // an implicit commit.
//                $connection->query('SET FOREIGN_KEY_CHECKS=1');
//                $connection->commit();
                foreach ($s1products as $s1product) {
//                    if ($prevName === strval($s1product['name'])) {
//                        $duplicates[] = strval($s1product['name']);
//                        continue;
//                    } else {
//                        $prevName = strval($s1product['name']);
//                    }


//                    dump(strval($s1product['id']));
//                    if (intval($s1product['id']) === 14953) {
//                        dump($s1product);
//                    }

                    $pr = $this->em->getRepository(Product::class)->find(intval($s1product['id']));
                    if ($pr) {
                        $pr->setSlug(strval($s1product['slug']));
                        $pr->setPrCode(strval($s1product['prCode']));
                        $pr->setBarcode(strval($s1product['mainBarcode']));
                        $pr->setProductName(strval($s1product['name']));
                        $pr->setImage(strval($s1product['imageUrl']));
                        $this->em->flush();
                    } else {
                        if (strval($s1product['id']) !== '') {
                            $pr = new Product();
                            $pr->setId(intval($s1product['id']));
                            $pr->setSlug(strval($s1product['slug']));
                            $pr->setPrCode(strval($s1product['prCode']));
                            $pr->setBarcode(strval($s1product['mainBarcode']));
                            $pr->setProductName(strval($s1product['name']));
                            $pr->setImage(strval($s1product['imageUrl']));
                            $this->em->persist($pr);
                            $this->em->flush();
//                    dump($pr);
                        }
                    }
                }
            }
            dump($duplicates);
            return;
        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
//            $connection->rollback();
            throw $e;
        }
    }

    public function synchronizeCategories(CronJobsService $service, LoggerInterface $logger)
    {
        try {
            // Todo: Check how to remove deleted category from S1
            $service->synchronizeCategories($this->authId);

            return new Response('Categories synchronization completed');
        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function syncCategoryTopSellers(CronJobsService $service, LoggerInterface $logger)
    {
        try {
            $service->syncCategoryTopSellers();

            return new Response('Category top sellers synchronization completed');
        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function synchronizeAvailabilityTypes(CronJobsService $service, LoggerInterface $logger)
    {
        try {
            $avTypes = $service->getAvailabilityTypes(-1, $this->authId);
            if ($avTypes) {
                foreach ($avTypes as $avType) {
                    dump($avType);
                    $at = new AvailabilityTypes();
                    $at->setS1id(intval($avType->ID));
                    $at->setName($avType->Name);
                    $at->setFromDays($avType->FromDays);
                    $at->setToDays($avType->ToDays);
                    $this->em->persist($at);
                }
                $this->em->flush();
            }
            return;
        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function synchronizeParameters(CronJobsService $service, LoggerInterface $logger)
    {
        try {
            $service->getParams();
            return;
        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function synchronizeCountries(CronJobsService $service, LoggerInterface $logger)
    {
        try {
            $service->getCountries();
            return;
        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}
