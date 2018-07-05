<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Service\{SoftoneLogin, CategoryService, CartService, ProductService};

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



    public function __construct(
        SoftoneLogin $softoneLogin,
        CategoryService $categoryService,
        CartService $cartService,
        ProductService $productService,
        SessionInterface $session,
        LoggerInterface $logger
    )
    {
        $this->softoneLogin = $softoneLogin;
        $this->categoryService = $categoryService;
        $this->cart = $cartService;
        $this->productService = $productService;
        $this->session = $session;
        $this->logger = $logger;

        $this->softoneLogin->login();
        $this->categories = $this->categoryService->getCategories();
        array_multisort(array_column($this->categories, "priority"), $this->categories);
        $this->popular = $productService->getCategoryItems(1022, $session->get("authID"));
        $this->featured = $productService->getCategoryItems(1008, $session->get("authID"));
        $this->loggedUser = (null !== $session->get("anosiaUser")) ?: null;
    }
}
