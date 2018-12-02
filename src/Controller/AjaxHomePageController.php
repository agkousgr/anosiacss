<?php


namespace App\Controller;


use App\Service\CategoryService;
use App\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class AjaxHomePageController extends AbstractController
{
    public function bestSellers(Request $request, ProductService $productService, CategoryService $categoryService)
    {
        $ctgId = ($request->query->get('ctgId')) ?: 1728;
        $subCategories = $categoryService->getChildrenCategories($ctgId);
        dump($subCategories);
        $bestSellers = $productService->getCategoryItems($subCategories, 0, 9, 'null', 'null', 'null', 1, 'null');

        return $this->render('home_page_modules/ajax_best_seller.html.twig', [
            'bestSellers' => $bestSellers
        ]);
    }
}