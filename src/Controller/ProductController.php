<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 13/5/2018
 * Time: 8:02 μμ
 */

namespace App\Controller;

use App\Service\{ProductService, SoftoneLogin, CategoryService};
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{

    public function listProducts(SoftoneLogin $softoneLogin, CategoryService $categoryService, int $id, ProductService $productService, LoggerInterface $logger, SessionInterface $session)
    {
        try {
            $softoneLogin->login();
            $categories = $categoryService->getCategories();
            array_multisort(array_column($categories, "priority"), $categories);
            $products = $productService->getProducts($id, $session->get("authID"));
            dump($products);
            return $this->render('products/list.html.twig', [
                'categories' => $categories,
                'products' => $products,
            ]);
        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function viewProduct(SoftoneLogin $softoneLogin, CategoryService $prCategories, int $id, ProductService $productService, LoggerInterface $logger, SessionInterface $session)
    {
        try {
            $softoneLogin->login();
            $categories = $prCategories->getCategories();
            array_multisort(array_column($categories, "priority"), $categories);
            $product = $productService->getProduct($id, $session->get("authID"));
            dump($product);
            return $this->render('products/view.html.twig', [
                'categories' => $categories,
                'pr' => $product,
            ]);
        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function quickviewProduct(SoftoneLogin $softoneLogin, CategoryService $prCategories, int $id, ProductService $productService, LoggerInterface $logger, SessionInterface $session)
    {
        try {
            $softoneLogin->login();
            $product = $productService->getProduct($id, $session->get("authID"));
            dump($product);
            return $this->render('products/quick_view.html.twig', [
                'pr' => $product,
            ]);
        } catch (\Exception $e) {
            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}