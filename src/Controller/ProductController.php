<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 13/5/2018
 * Time: 8:02 μμ
 */

namespace App\Controller;


use Symfony\Component\HttpFoundation\Request;

class ProductController extends MainController
{

    public function listProducts(int $id)
    {
        try {
            $ctgInfo = $this->categoryService->getCategoryInfo($id);
            $products = $this->productService->getCategoryItems($id);
            $totalProducts = $this->productService->getCategoryItemsCount($id);
//            $pagination = $knp->paginate(
//                $products,
//                $request->query->getInt('page', 1)/*page number*/,
//                10/*limit per page*/
//            );
            return $this->render('products/list.html.twig', [
                'products' => $products,
                'ctgInfo' => $ctgInfo,
                'categories' => $this->categories,
                'popular' => $this->popular,
                'featured' => $this->featured,
                'cartItems' => $this->cartItems,
                'totalCartItems' => $this->totalCartItems,
                'loggedUser' => $this->loggedUser,
//                'pagination' => $pagination
            ]);
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function viewProduct(int $id)
    {
        try {
            $product = $this->productService->getItems($id, 'null');
            return $this->render('products/view.html.twig', [
                'pr' => $product,
                'categories' => $this->categories,
                'popular' => $this->popular,
                'featured' => $this->featured,
                'cartItems' => $this->cartItems,
                'totalCartItems' => $this->totalCartItems,
                'loggedUser' => $this->loggedUser,
            ]);
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function searchResults(Request $request)
    {
        try {
            $keyword = strip_tags(trim($request->request->get('keyword')));
            $s1Keyword = preg_replace('!\s+!', ',', $keyword);
            dump($keyword);
            $products = $this->productService->getItems('null', $s1Keyword);
            return $this->render('products/search.html.twig', [
                'products' => $products,
                'categories' => $this->categories,
                'popular' => $this->popular,
                'featured' => $this->featured,
                'cartItems' => $this->cartItems,
                'totalCartItems' => $this->totalCartItems,
                'loggedUser' => $this->loggedUser,
                'keyword' => $keyword
            ]);
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

//    public function quickviewProduct(SoftoneLogin $softoneLogin, CategoryService $prCategories, int $id, ProductService $productService, LoggerInterface $logger, SessionInterface $session)
//    {
//        try {
//            $softoneLogin->login();
//            $product = $productService->getProduct($id, $session->get("authID"));
////            dump($product);
//            return $this->render('products/quick_view.html.twig', [
//                'pr' => $product,
//            ]);
//        } catch (\Exception $e) {
//            $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
//            throw $e;
//        }
//    }
}