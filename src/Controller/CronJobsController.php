<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 4/8/2018
 * Time: 11:51 μμ
 */

namespace App\Controller;

use App\Entity\AvailabilityTypes;
use App\Entity\Category;
use App\Service\CategoryService;
use App\Service\CronJobsService;
use App\Service\SoftoneLogin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Psr\Log\LoggerInterface;

class CronJobsController extends AbstractController
{
    /**
     * @var CategoryService
     */
    private $categoryService;

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

    public function __construct(CronJobsService $cronJobsService, CategoryService $categoryService, EntityManagerInterface $em, SoftoneLogin $softoneLogin, LoggerInterface $logger)
    {
        $this->categoryService = $categoryService;
        $this->em = $em;
        $this->cronJobsService = $cronJobsService;
        $this->logger = $logger;
        $this->authId = $softoneLogin->login();
    }

    public function synchronizeCategories(EntityManagerInterface $em)
    {
        try {
            // Todo: Check how to remove deleted category from S1
            $categories = $this->cronJobsService->synchronizeCategories($this->authId);
            foreach ($categories as $val) {
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
                    $parent = $em->getRepository(Category::class)->find($val['parentId']);
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
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    private function createChild($subCategories, $parentId)
    {
        try {
            foreach ($subCategories as $val) {
                $categoryExists = $this->em->getRepository(Category::class)->find($val["id"]);
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
}