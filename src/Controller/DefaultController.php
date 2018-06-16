<?php

namespace App\Controller;

use App\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Service\{SoftoneLogin, CategoryService};

class DefaultController extends AbstractController
{
    public function index(SoftoneLogin $softoneLogin, CategoryService $categoryService, ProductService $pr, SessionInterface $session)
    {

        $softoneLogin->login();
        $categories = $categoryService->getCategories();
        array_multisort(array_column($categories, "priority"), $categories);
        // Load categories from XML format to array
//        $ctgEntity = $prCategories->InitializeCategories($categoriesXML, $session->get("authID"));

        $featured = $pr->getProducts(1020, $session->get("authID"));
        $popular = $pr->getProducts(1022, $session->get("authID"));
//        dump($categories, $featured);
//        return new Response(dump($categories));

        return $this->render('layout.html.twig', [
            'categories' => $categories,
            'featured' => $featured,
            'popular' => $popular
        ]);
    }
}