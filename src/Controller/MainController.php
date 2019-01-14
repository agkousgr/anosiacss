<?php

namespace App\Controller;

use App\Entity\{Cart, Category, Wishlist};
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Service\{
    SoftoneLogin, CategoryService, CartService, ProductService
};
use Facebook\Facebook;

class MainController extends AbstractController
{
    /**
     * @var \App\Service\SoftoneLogin
     */
    protected $softoneLogin;

    /**
     * @var \App\Service\CategoryService
     */
    protected $categoryService;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var \App\Service\CartService
     */
    protected $cart;

    /**
     * @var int
     */
    protected $totalCartItems;

    /**
     * @var int
     */
    protected $totalWishlistItems;

    /**
     * @var \App\Service\ProductService
     */
    protected $productService;

    /**
     * @var array
     */
    protected $categories;

    /**
     * @var array
     */
    protected $popular;

    /**
     * @var array
     */
    protected $featured;

    /**
     * @var string
     */
    protected $loggedUser;

    /**
     * @var string
     */
    protected $loggedName;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var array
     */
    protected $cartItems;

    /**
     * @var int
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $cache_expire;

    /**
     * @var string
     */
    protected $loginUrl;

    /**
     * @var Facebook
     */
    protected $fb;


    public function __construct(
        SoftoneLogin $softoneLogin,
        CategoryService $categoryService,
        CartService $cartService,
        ProductService $productService,
        SessionInterface $session,
        LoggerInterface $logger,
        EntityManagerInterface $em
    )
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
////            session_cache_expire(180);
////            $session->set('cache_expire', session_cache_expire());
        }

        $this->softoneLogin = $softoneLogin;
        $this->categoryService = $categoryService;
        $this->cart = $cartService;
        $this->productService = $productService;
        $this->session = $session;
        $this->logger = $logger;
        $this->em = $em;
        $this->totalCartItems = 0;
        $this->totalWishlistItems = 0;

        $this->fb = new Facebook([
            'app_id' => '605092459847380',
            'app_secret' => '09f4a59ad57726736664a92d7059025f',
            'default_graph_version' => 'v3.0',
            'default_access_token' => 'EAAImVBEgHtQBAFhb99ycIu6WWZAOdOO3lb0M4M9q4aFOoSCZA4G2fd7W9LpsBZAoCELeykpMST4ZAOmVhygKT13rGRoMd4RXL1lqXGbzds0U1ZB3LTqhuRBMkb3r1pG6lLsaSAwHdMTXTmZB8u0KiKldQmo30Vy3VlJooKKK9agViGBc8zMi8nLW0KKZA9zXCpbfZCcV9sCVgeP7XpZCyZABrC', // optional
        ]);
        $helper = $this->fb->getRedirectLoginHelper();
        $permissions = ['email']; // Optional permissions
        $this->loginUrl = $helper->getLoginUrl('https://new.anosiapharmacy.gr/fb-callback', $permissions);

        if (!$this->session->get('categories')) {
            dump('no categories');
            $this->categories = $this->categoryService->getCategoriesFromS1();
            dump($this->categories);
//            die();
            array_multisort(array_column($this->categories, "priority"), $this->categories);
            $this->session->set('categories', $this->categories);
        }

//        $this->categories = $this->em->getRepository(Category::class)->childrenHierarchy();
        $this->categories = $this->session->get('categories');
        if ($this->categories) {
        }
        $this->popular = $productService->getCategoryItems(1867, 0, 15, 'null', 'null');
        $this->featured = $productService->getCategoryItems(1867, 0, 15, 'null', 'null');
//        $this->popular = [];
//        $this->featured = [];
//        $mainCategoriesArr = [];
//        foreach ($this->categories as $category) {
//            $mainCategories[$category['s1id']][] = $this->productService->getRelevantItems(-1, 300, 1, 1, 1, 1922);
//        }
//        dump($mainCategories);
//        $manuItems = $this->productService->getRelevantItems(-1, -1, 1, 1, 1);
        $this->loggedUser = ($this->session->get("anosiaUser")) ?: null;
        $this->loggedName = ($this->session->get("anosiaName")) ?: null;
        $this->loggedClientId = ($this->session->get("anosiaClientId")) ?: null;

        $this->cartItems = $this->getCartItems();
        $this->totalWishlistItems = $this->em->getRepository(Wishlist::class)->countWishlistItems($this->session->getId(), $this->loggedUser);


//        $this->totalCartItems = $em->getRepository(Cart::class)->countCartItems($session->getId(), $session->get('anosiaUser'));
    }

    protected function getCartItems()
    {
        $cartIds = '';
        if (null !== $this->loggedUser) {
            $cartArr = $this->em->getRepository(Cart::class)->getCartByUser($this->loggedUser);
        } else {
            $cartArr = $this->em->getRepository(Cart::class)->getCartBySession($this->session->getId());
        }

        if ($cartArr) {
            foreach ($cartArr as $key => $val) {
                $cartIds .= $val->getProductId() . ',';
//                $this->totalCartItems = $this->totalCartItems + 1*$val->getQuantity();
                $this->totalCartItems = $this->totalCartItems + 1;
            }
            $cartIds = substr($cartIds, 0, -1);
        }
        return ($cartIds) ? $this->cart->getCartItems($cartIds, $cartArr, count($cartArr)) : '';
    }

}
