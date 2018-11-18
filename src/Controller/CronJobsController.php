<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 4/8/2018
 * Time: 11:51 μμ
 */

namespace App\Controller;

use App\Entity\Category;
use App\Service\CategoryService;
use App\Service\CronJobsService;
use App\Service\SoftoneLogin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
     * @var SoftoneLogin
     */
    private $softoneLogin;

    public function __construct(CategoryService $categoryService, EntityManagerInterface $em, SoftoneLogin $softoneLogin)
    {
        $this->categoryService = $categoryService;
        $this->em = $em;
        $this->softoneLogin = $softoneLogin;
    }

    public function synchronizeCategories(CronJobsService $cronJobsService, EntityManagerInterface $em, SoftoneLogin $softoneLogin)
    {
        $authID = $softoneLogin->login();
        $categories = $cronJobsService->synchronizeCategories($authID);
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
    }

    private function createChild($subCategories, $parentId)
    {
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
    }
}