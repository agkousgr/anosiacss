<?php

namespace App\Controller;

use App\Entity\AvailabilityTypes;
use App\Entity\Category;
use App\Entity\Products;
use App\Service\CategoryService;
use App\Service\CronJobsService;
use App\Service\ProductService;
use App\Service\SoftoneLogin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Psr\Log\LoggerInterface;

class CronJobsController extends AbstractController
{
    /**
     * @var CategoryService
     */
    private $categoryService;

    /**
     * @var ProductService
     */
    private $productService;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var string
     */
    private $authId;

    /**
     * @var CronJobsService
     */
    private $cronJobsService;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(CronJobsService $cronJobsService, ProductService $productService, CategoryService $categoryService, EntityManagerInterface $em, SoftoneLogin $softoneLogin, LoggerInterface $logger)
    {
        $this->categoryService = $categoryService;
        $this->productService = $productService;
        $this->em = $em;
        $this->cronJobsService = $cronJobsService;
        $this->logger = $logger;
        $this->authId = $softoneLogin->login();
    }

    public function synchronizeProducts()
    {
//        $cmd = $this->em->getClassMetadata($pr);
        $connection = $this->em->getConnection();
        $connection->beginTransaction();
        $prevName = '';
        $duplicates = [];
        try {
            $s1products = $this->productService->getItems('null', 'null', 4000, 'NameDesc', -1, 'null', 'null', 'null', 1, 'null', 0);
            if ($s1products) {
//                $connection->query('SET FOREIGN_KEY_CHECKS=0');
//                $connection->query('DELETE FROM products');
//                // Beware of ALTER TABLE here--it's another DDL statement and will cause
//                // an implicit commit.
//                $connection->query('SET FOREIGN_KEY_CHECKS=1');
//                $connection->commit();
                foreach ($s1products as $s1product) {
                    if ($prevName === strval($s1product['name'])) {
                        $duplicates[] = strval($s1product['name']);
                        continue;
                    } else {
                        $prevName = strval($s1product['name']);
                    }


//                    dump(strval($s1product['id']));
//                    if (intval($s1product['id']) === 14953) {
//                        dump($s1product);
//                    }

                    $pr = $this->em->getRepository(Products::class)->find(intval($s1product['id']));
                    if ($pr) {
                        $pr->setSlug(strval($s1product['slug']));
                        $pr->setPrCode(strval($s1product['prCode']));
                        $pr->setBarcode(strval($s1product['mainBarcode']));
                        $pr->setProductName(strval($s1product['name']));
                        $pr->setImage(strval($s1product['imageUrl']));
                        $this->em->persist($pr);
                        $this->em->flush();
                    } else {
                        if (strval($s1product['id']) !== '') {
                            $pr = new Products();
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
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
            $connection->rollback();
        }
    }

    public function synchronizeCategories()
    {
        try {
            // Todo: Check how to remove deleted category from S1
            $categories = $this->cronJobsService->synchronizeCategories($this->authId);
            foreach ($categories as $val) {
                dump($val);
//                continue;
                $categoryExists = $this->em->getRepository(Category::class)->find($val["id"]);
                if ($categoryExists) {
                    $category = $categoryExists;
                } else {
                    $category = new Category();
                }
                $isVisible = ((string)$val["isVisible"] === '1') ? true : false;
                $category->setId((int)$val["id"]);
                $category->setName($val["name"]);
                $category->setSlug($val["slug"]);
                $category->setPriority($val["priority"]);
                $category->setS1id((int)$val["id"]);
                $category->setDescription($val["description"]);
                $category->setImageUrl($val["imageUrl"]);
                if ($val['parentId'] > 0) {
                    $parent = $this->em->getRepository(Category::class)->find($val['parentId']);
                    $category->setParent($parent);
                }
                $category->setIsVisible($isVisible);
                $category->setItemsCount(0);
                $this->em->persist($category);
                $this->em->flush();
                if (!empty($val["children"])) {
                    $this->createChild($val["children"], (int)$val["id"]);
                }
            }
//        dump($category);
            return;
            return new Response('Categories synchronization completed');
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    private function createChild($subCategories, $parentId)
    {
        try {
            $subCtgsArr = explode(',', $subCategories);
            foreach ($subCtgsArr as $val) {
                dump($val);
                $categoryExists = $this->em->getRepository(Category::class)->find((int)$val["id"]);
                $parentCategory = $this->em->getRepository(Category::class)->find($parentId);
                dump($categoryExists, $parentCategory);
                if ($categoryExists) {
                    $category = $categoryExists;
                } else {
                    $category = new Category();
                }
                $isVisible = ((string)$val["isVisible"] === '1') ? true : false;
                $category->setId((int)$val["id"]);
                $category->setName($val["name"]);
                $category->setSlug($val["slug"]);
                $category->setParent($parentCategory);
                $category->setPriority($val["priority"]);
                $category->setS1id((int)$val["id"]);
                $category->setDescription($val["description"]);
                $category->setImageUrl($val["imageUrl"]);
                $category->setIsVisible($isVisible);
                $category->setItemsCount(0);
                $this->em->persist($category);
                $this->em->flush();
                if (!empty($val["children"])) {
                    $this->createChild($val["children"], (int)$val["id"]);
                }
            }
            return;
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function synchronizeAvailabilityTypes()
    {
        try {
            $avTypes = $this->cronJobsService->getAvailabilityTypes(-1, $this->authId);
            if ($avTypes) {
                foreach ($avTypes as $avType) {
                    dump($avType);
                    $at = new AvailabilityTypes();
                    $at->setS1id(intval($avType->ID));
                    $at->setName($avType->Name);
                    $at->setFromDays($avType->FromDays);
                    $at->setToDays($avType->ToDays);
                    $this->em->persist($at);
                    $this->em->flush();
                }
            }
            return;
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function test()
    {
        dump('xong');
        return;
    }

    public function synchronizeParameters(CronJobsService $service)
    {
        try {
            $service->getParams();
            return;
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}