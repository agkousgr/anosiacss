<?php

namespace App\Controller;

use App\Entity\{AlsoViewedProducts, Category, Product, Slider, ProductViews};
use App\Service\BrandsService;
use App\Service\ProductService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends MainController
{
    public function listProducts(Request $request, int $page, string $slug, BrandsService $brandsService, PaginatorInterface $paginator)
    {
        try {
            $pagesize = $request->query->getInt('pagesize') ?: 12;
            $sortBy = $request->query->get('sortBy') ?: 'NameAsc';
            $mnufacturerId = $request->query->get('brands') ? str_replace('-', ',', $request->query->get('brands')) : 'null';
            $priceRange = $request->query->get('priceRange') ?: 'null';
            $category = $this->em->getRepository(Category::class)->findOneBy(['slug' => $slug]);
            $brands = $brandsService->getCategoryManufacturers($category->getS1id());
            $subCategories = $category->getChildren();
            $slider = $this->em->getRepository(Slider::class)->findBy(['category' => $category]);
            $productsCountArr = $this->productService->getCategoryItemsCount($category->getS1id(), 'null', $priceRange, 1, $mnufacturerId);
            $productsCount = intval($productsCountArr->Count);
            $minPrice = intval($productsCountArr->MinPrice);
            $maxPrice = intval($productsCountArr->MaxPrice);
            $products = $this->productService->getCategoryItems($category->getS1id(), 0, 10000, $sortBy, 'null', $priceRange, 1, $mnufacturerId);
            $pagination = $paginator->paginate($products, $page, $pagesize);

            return $this->render('products/list.html.twig', [
                'ctgInfo' => $category,
                'categories' => $this->categories,
                'topSellers' => $this->topSellers,
                'popular' => $this->popular,
                'featured' => $this->featured,
                'cartItems' => $this->cartItems,
                'totalCartItems' => $this->totalCartItems,
                'totalWishlistItems' => $this->totalWishlistItems,
                'loggedUser' => $this->loggedUser,
                'loggedName' => $this->loggedName,
                'slider' => $slider,
                'productsCount' => $productsCount,
                'minPrice' => $minPrice,
                'maxPrice' => $maxPrice,
                'brands' => $brands,
                'pagesize' => $pagesize,
                'sortBy' => $sortBy,
                'brand' => $mnufacturerId,
                'priceRange' => $priceRange,
                'loginUrl' => $this->loginUrl,
                'subCategories' => $subCategories,
                'pagination' => $pagination
            ]);
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function listBrands(Request $request, int $page, BrandsService $brandsService, PaginatorInterface $paginator)
    {
        $brands = $brandsService->getManufacturers('null');
        $paginatedBrands = $paginator->paginate(
            $brands,
            $page /*page number*/,
            15/*limit per page*/
        );
        $paginatedBrands->setUsedRoute('products_list');
        $paginatedBrands->setTemplate('paginator_template/override_template.html.twig');
        $paginatedBrands->setSortableTemplate('paginator_template/override_sortable.html.twig');

        return $this->render('products/brands_list.html.twig', [
            'categories' => $this->categories,
            'topSellers' => $this->topSellers,
            'popular' => $this->popular,
            'featured' => $this->featured,
            'cartItems' => $this->cartItems,
            'totalCartItems' => $this->totalCartItems,
            'totalWishlistItems' => $this->totalWishlistItems,
            'loggedUser' => $this->loggedUser,
            'loggedName' => $this->loggedName,
            'brands' => $paginatedBrands,
            'loginUrl' => $this->loginUrl
//            'slider' => $slider
        ]);
    }

    public function listBrandProducts(Request $request, string $slug, PaginatorInterface $paginator, BrandsService $brandsService)
    {
        try {
//            $ctgInfo = $this->categoryService->getCategoryInfo($id);
            $brandInfo = $brands = $brandsService->getManufacturers($slug);
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
                'topSellers' => $this->topSellers,
                'popular' => $this->popular,
                'featured' => $this->featured,
                'cartItems' => $this->cartItems,
                'totalCartItems' => $this->totalCartItems,
                'totalWishlistItems' => $this->totalWishlistItems,
                'loggedUser' => $this->loggedUser,
                'loggedName' => $this->loggedName,
                'loginUrl' => $this->loginUrl
//                'slider' => $slider
//                'products' => $paginatedProducts
            ]);
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function viewProduct(string $slug, EntityManagerInterface $em, ProductService $productService)
    {
        try {
//            $alsoViewed = new AlsoViewedProducts();
            $pr = $em->getRepository(Product::class)->findOneBy(['slug' => $slug]);

            $product = $this->productService->getItems($pr->getId(), 'null', 10);
            $relativeProducts = $productService->getRelevantItems('null', -1, 1, 1, 0, $product['categories'][0]->getS1id());

            $pr->setViews($pr->getViews() + 1);
            $em->flush();
            return $this->render('products/view.html.twig', [
                'pr' => $product,
                'categories' => $this->categories,
                'topSellers' => $this->topSellers,
                'relativeProducts' => $relativeProducts,
                'cartItems' => $this->cartItems,
                'totalCartItems' => $this->totalCartItems,
                'totalWishlistItems' => $this->totalWishlistItems,
                'loggedUser' => $this->loggedUser,
                'loggedName' => $this->loggedName,
                'loginUrl' => $this->loginUrl
            ]);
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function searchResults(Request $request, int $page, PaginatorInterface $paginator)
    {
        try {
            $keyword = strip_tags(trim($request->query->get('keyword')));
            $s1Keyword = preg_replace('!\s+!', ',', $keyword);
            $pagesize = $request->query->get('pagesize') ?: 12;
            $sortBy = $request->query->get('sortBy') ?: 'NameAsc';
            $priceRange = ($request->query->get('priceRange')) ?: 'null';
            $productsCount = $this->productService->getItemsCount($s1Keyword, 'null', $priceRange, 1, 'null');
            $products = $this->productService->getItems(
                'null', $s1Keyword, 10000, $sortBy, '-1', 'null', $priceRange, 'null', 1, 'null', 0
            );
            $pagination = $paginator->paginate($products, $page, $pagesize);

            return $this->render('products/search.html.twig', [
                'categories' => $this->categories,
                'topSellers' => $this->topSellers,
                'popular' => $this->popular,
                'featured' => $this->featured,
                'cartItems' => $this->cartItems,
                'totalCartItems' => $this->totalCartItems,
                'totalWishlistItems' => $this->totalWishlistItems,
                'loggedUser' => $this->loggedUser,
                'loggedName' => $this->loggedName,
                'keyword' => $keyword,
                'loginUrl' => $this->loginUrl,
                'productsCount' => $productsCount,
                'page' => $page,
                'pagesize' => $pagesize,
                'sortBy' => $sortBy,
                'priceRange' => $priceRange,
                'pagination' => $pagination,
            ]);
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function searchAnosiaResults(Request $request, int $page, PaginatorInterface $paginator)
    {
        try {
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
                'topSellers' => $this->topSellers,
                'popular' => $this->popular,
                'featured' => $this->featured,
                'cartItems' => $this->cartItems,
                'totalCartItems' => $this->totalCartItems,
                'totalWishlistItems' => $this->totalWishlistItems,
                'loggedUser' => $this->loggedUser,
                'loggedName' => $this->loggedName,
                'keyword' => $keyword,
                'loginUrl' => $this->loginUrl
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