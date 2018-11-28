<?php

namespace App\Controller;

use App\Entity\{AlsoViewedProducts, Category, Slider, ProductViews};
use App\Service\BrandsService;
use App\Service\ProductService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends MainController
{

    public function listProducts(Request $request, int $page, int $id, BrandsService $brandsService)
    {
        try {

            $pagesize = ($request->query->get('pagesize')) ? preg_replace('/[^A-Za-z0-9\-]/', '', $request->query->get('pagesize')) : 12;
            $sortBy = ($request->query->get('sortBy')) ?: 'NameAsc';
            $makeId = ($request->query->get('brands')) ? str_replace('-', ',', $request->query->get('brands')) : 'null';
            $priceRange = ($request->query->get('priceRange')) ?: 'null';
            $brands = $brandsService->getManufacturers('null');
            $ctgInfo = $this->em->getRepository(Category::class)->find($id);

            dump($ctgInfo->getChildren());
            $subCategories = $this->em->getRepository(Category::class)->findBy(['parent' => $ctgInfo]);
            $slider = $this->em->getRepository(Slider::class)->findBy(['category' => $id]);
            $productsCountArr = $this->productService->getCategoryItemsCount($id, $makeId, $priceRange);
//            dump($productsCountArr);
            $productsCount = intval($productsCountArr->Count);
            $minPrice = intval($productsCountArr->MinPrice);
            $maxPrice = intval($productsCountArr->MaxPrice);
            if ($productsCount > $pagesize * $page) {
                $products = $this->productService->getCategoryItems($id, $page - 1, $pagesize, $sortBy, $makeId, $priceRange);
            } else {
                $products = $this->productService->getCategoryItems($id, 0, $pagesize, $sortBy, $makeId, $priceRange);
            }
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
                'minPrice' => $minPrice,
                'maxPrice' => $maxPrice,
                'brands' => $brands,
                'page' => $page,
                'pagesize' => $pagesize,
                'sortBy' => $sortBy,
                'brand' => $makeId,
                'priceRange' => $priceRange,
                'loginUrl' => $this->loginUrl,
                'subCategories' => $subCategories
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
                'loginUrl' => $this->loginUrl
//                'slider' => $slider
//                'products' => $paginatedProducts
            ]);
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function viewProduct(Request $request, int $id, EntityManagerInterface $em, ProductService $productService)
    {
        try {
//            $getReferrer = $productService->getReferer($request->server->get('HTTP_REFERER'));
            $alsoViewed = new AlsoViewedProducts();
            $product = $this->productService->getItems($id, 'null', 1);
            dump($product);
            $productId = intval($product["id"]);
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
                'loginUrl' => $this->loginUrl
            ]);
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function searchResults(Request $request, int $page)
    {
        try {
            dump($request);
            $keyword = strip_tags(trim($request->request->get('keyword')));
            $s1Keyword = preg_replace('!\s+!', ',', $keyword);

            $pagesize = ($request->query->get('pagesize')) ? preg_replace('/[^A-Za-z0-9\-]/', '', $request->query->get('pagesize')) : 12;
            $sortBy = ($request->query->get('sortBy')) ?: 'NameAsc';
            $makeId = ($request->query->get('brands')) ? str_replace('-', ',', $request->query->get('brands')) : 'null';
            $priceRange = ($request->query->get('priceRange')) ?: 'null';


            $productsCount = $this->productService->getItemsCount($s1Keyword, $makeId, $priceRange);
            if ($productsCount > $pagesize * $page) {
                $products = $this->productService->getItems('null', $s1Keyword, $pagesize, $sortBy, -1, $makeId, $priceRange);
            } else {
                $products = $this->productService->getItems('null', $s1Keyword, $pagesize, $sortBy, -1, $makeId, $priceRange);
            }


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
                'keyword' => $keyword,
                'loginUrl' => $this->loginUrl,
                'productsCount' => $productsCount
            ]);
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function searchAnosiaResults(Request $request, int $page, PaginatorInterface $paginator)
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