<?php

namespace App\Controller;

use App\Entity\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Service\{
    SoftoneLogin, CategoryService, CartService, ProductService
};

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
        dump($session);
        $this->softoneLogin = $softoneLogin;
        $this->categoryService = $categoryService;
        $this->cart = $cartService;
        $this->productService = $productService;
        $this->session = $session;
        $this->logger = $logger;
        $this->em = $em;
        $this->totalCartItems = 0;

        $this->softoneLogin->login();
        $this->categories = $this->categoryService->getCategories();
        array_multisort(array_column($this->categories, "priority"), $this->categories);
        $this->popular = $productService->getCategoryItems(1022);
        $this->featured = $productService->getCategoryItems(1008);
        $this->loggedUser = ($session->get("anosiaUser")) ?: null;
        $this->loggedName = ($session->get("anosiaName")) ?: null;
        $this->loggedClientId = ($session->get("anosiaClientId")) ?: null;
        $this->cartItems = $this->getCartItems();

//        $this->totalCartItems = $em->getRepository(Cart::class)->countCartItems($session->getId(), $session->get('anosiaUser'));
    }

    protected function getCartItems() {
        $cartIds = '';
        if (null === $this->session->get('username')) {
            $cartArr = $this->em->getRepository(Cart::class)->getCartBySession($this->session->getId());
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
            dump($this->totalCartItems);
        }
        return ($cartIds) ? $this->cart->getCartItems($cartIds, $cartArr) : '';
    }
}
