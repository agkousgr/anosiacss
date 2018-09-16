<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\ProductViews;
use App\Entity\Slider;
use App\Service\BrandsService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends MainController
{

    public function listProducts(Request $request, int $page, int $id, BrandsService $brandsService)
    {
        try {
            dump($request);
            $pagesize = ($request->query->get('pagesize')) ? preg_replace('/[^A-Za-z0-9\-]/', '', $request->query->get('pagesize')) : 12;
            $sortBy = ($request->query->get('sortBy')) ?: 'NameAsc';
            $makeId = ($request->query->get('brands')) ? str_replace('-', ',', $request->query->get('brands')) : 'null';
            $priceRange = '';
            $brands = $brandsService->getBrands('null');
            $ctgInfo = $this->em->getRepository(Category::class)->find($id);
            $slider = $this->em->getRepository(Slider::class)->findBy(['category' => $id]);
            $products = $this->productService->getCategoryItems($id, $page - 1, $pagesize, $sortBy, $makeId);
            $productsCount = $this->productService->getCategoryItemsCount($id, $makeId);
            dump($makeId, $products);
            return $this->render('products/list.html.twig', [
                'products' => $products,
                'ctgInfo' => $ctgInfo,
                'categories' => $this->categories,
                'popular' => $this->popular,
                'featured' => $this->featured,
                'cartItems' => $this->cartItems,
                'totalCartItems' => $this->totalCartItems,
                'totalWishlistItems' => $this->totalWishlistItems,
                'loggedUser' => $this->loggedUser,
                'loggedName' => $this->loggedName,
                'slider' => $slider,
                'productsCount' => $productsCount,
                'brands' => $brands,
                'page' => $page,
                'pagesize' => $pagesize,
                'sortBy' => $sortBy,
                'brand' => $makeId,
                'priceRange' => $priceRange
            ]);
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function listBrands(Request $request, int $page, BrandsService $brandsService, PaginatorInterface $paginator)
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
            $paginatedProducts->setUsedRoute('brand_products_list');
            $paginatedProducts->setTemplate('paginator_template/override_template.html.twig');
            $paginatedProducts->setSortableTemplate('paginator_template/override_sortable.html.twig');
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

    public function viewProduct(int $id, EntityManagerInterface $em)
    {
        try {
            $product = $this->productService->getItems($id, 1, 1, 'null', 'null');
            $productId = intval($product["id"]);
            dump($product);
            $productView = $em->getRepository(ProductViews::class)->findOneBy(['product_id' => $productId]);
            if (empty($productView)) {
                $productView = new ProductViews();
                $productView->setViews(1);
                $productView->setProductId($productId);
                $em->persist($productView);
            } else {
                $productView->setViews($productView->getViews() + 1);
            }
            $em->flush();
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

    public function searchResults(Request $request, int $page, PaginatorInterface $paginator)
    {
        try {
            dump($request);
            $keyword = strip_tags(trim($request->request->get('keyword')));
            $s1Keyword = preg_replace('!\s+!', ',', $keyword);
            $products = $this->productService->getItems('null', $s1Keyword, 1000);
            $paginatedProducts = $paginator->paginate(
                $products,
                $page /*page number*/,
                12/*limit per page*/
            );
            $paginatedProducts->setUsedRoute('product_search');
            $paginatedProducts->setTemplate('paginator_template/override_template.html.twig');
            $paginatedProducts->setSortableTemplate('paginator_template/override_sortable.html.twig');
            return $this->render('products/search.html.twig', [
                'products' => $paginatedProducts,
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