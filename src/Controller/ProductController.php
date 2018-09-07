<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Slider;
use App\Service\BrandsService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends MainController
{

    public function listProducts(Request $request, int $id, int $page, PaginatorInterface $paginator)
    {
        try {
//            $ctgInfo = $this->categoryService->getCategoryInfo($id);
            $sort = preg_replace('/[^A-Za-z0-9\-]/', '', $request->query->get('sort'));
            $direction = $request->query->get('direction');
            $ctgInfo = $this->em->getRepository(Category::class)->find($id);
            $slider = $this->em->getRepository(Slider::class)->findBy(['category' => $id]);
            $products = $this->productService->getCategoryItems($id);
            dump($sort);
            if ($sort) {
                array_multisort(array_column($products, $sort), $products);
            }
            $paginatedProducts = $paginator->paginate(
                $products,
                $page /*page number*/,
                12/*limit per page*/
            );
            $paginatedProducts->setUsedRoute('products_list');
            $paginatedProducts->setTemplate('paginator_template/override_template.html.twig');
            $paginatedProducts->setSortableTemplate('paginator_template/override_sortable.html.twig');
            dump($request, $paginatedProducts);
            return $this->render('products/list.html.twig', [
                'products' => $paginatedProducts,
                'ctgInfo' => $ctgInfo,
                'categories' => $this->categories,
                'popular' => $this->popular,
                'featured' => $this->featured,
                'cartItems' => $this->cartItems,
                'totalCartItems' => $this->totalCartItems,
                'totalWishlistItems' => $this->totalWishlistItems,
                'loggedUser' => $this->loggedUser,
                'loggedName' => $this->loggedName,
                'slider' => $slider
//                'products' => $paginatedProducts
            ]);
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function listBrands(Request $request, int $page, BrandsService $brandsService, PaginatorInterface  $paginator)
    {
        $brands = $brandsService->getBrands('null');
        $paginatedBrands = $paginator->paginate(
            $brands,
            $page /*page number*/,
            15/*limit per page*/
        );
        $paginatedBrands->setUsedRoute('products_list');
        $paginatedBrands->setTemplate('paginator_template/override_template.html.twig');
        $paginatedBrands->setSortableTemplate('paginator_template/override_sortable.html.twig');
        dump($brands);
        return $this->render('products/brands_list.html.twig', [
            'categories' => $this->categories,
            'popular' => $this->popular,
            'featured' => $this->featured,
            'cartItems' => $this->cartItems,
            'totalCartItems' => $this->totalCartItems,
            'totalWishlistItems' => $this->totalWishlistItems,
            'loggedUser' => $this->loggedUser,
            'loggedName' => $this->loggedName,
            'brands' => $paginatedBrands
//            'slider' => $slider
        ]);
    }

    public function listBrandProducts(Request $request, string $slug, PaginatorInterface $paginator, BrandsService $brandsService)
    {
        try {
//            $ctgInfo = $this->categoryService->getCategoryInfo($id);
            $brandInfo = $brands = $brandsService->getBrands($slug);
            dump($brandInfo);
//            $slider = $this->em->getRepository(Slider::class)->findBy(['category' => $id]);
            $products = $this->productService->getItems('null', 'null', 1000, 'null', '-1', $brandInfo[0]["id"]);

//            $totalProducts = $this->productService->getCategoryItemsCount($id);
//            $totalProducts = 15;
            $paginatedProducts = $paginator->paginate(
                $products,
                $request->query->getInt('page', 1)/*page number*/,
                12/*limit per page*/
            );
//            $paginator->setTemplate('bundles/KnpPaginator/twitter_bootstrap_v3_pagination.html.twig');
            return $this->render('products/brand_products_list.html.twig', [
                'products' => $paginatedProducts,
                'brandInfo' => $brandInfo,
                'categories' => $this->categories,
                'popular' => $this->popular,
                'featured' => $this->featured,
                'cartItems' => $this->cartItems,
                'totalCartItems' => $this->totalCartItems,
                'totalWishlistItems' => $this->totalWishlistItems,
                'loggedUser' => $this->loggedUser,
                'loggedName' => $this->loggedName,
//                'slider' => $slider
//                'products' => $paginatedProducts
            ]);
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function viewProduct(int $id)
    {
        try {
            $product = $this->productService->getItems($id, 'null', 1);
            return $this->render('products/view.html.twig', [
                'pr' => $product,
                'categories' => $this->categories,
                'popular' => $this->popular,
                'featured' => $this->featured,
                'cartItems' => $this->cartItems,
                'totalCartItems' => $this->totalCartItems,
                'totalWishlistItems' => $this->totalWishlistItems,
                'loggedUser' => $this->loggedUser,
                'loggedName' => $this->loggedName,
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
            $products = $this->productService->getItems('null', $s1Keyword, 60);
            return $this->render('products/search.html.twig', [
                'products' => $products,
                'categories' => $this->categories,
                'popular' => $this->popular,
                'featured' => $this->featured,
                'cartItems' => $this->cartItems,
                'totalCartItems' => $this->totalCartItems,
                'totalWishlistItems' => $this->totalWishlistItems,
                'loggedUser' => $this->loggedUser,
                'loggedName' => $this->loggedName,
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