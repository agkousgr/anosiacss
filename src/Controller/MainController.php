<?php

namespace App\Controller;

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
     * @var string
     */
    protected $loggedUser;

    public function __construct(
        SoftoneLogin $softoneLogin,
        CategoryService $categoryService,
        CartService $cartService,
        ProductService $productService,
        SessionInterface $session
    )
    {
        $this->softoneLogin = $softoneLogin;
        $this->categoryService = $categoryService;
        $this->cart = $cartService;
        $this->productService = $productService;
        $this->session = $session;

        $this->softoneLogin->login();
        $this->categories = $this->categoryService->getCategories();
        $this->popular = $productService->getCategoryItems(1022, $session->get("authID"));
        $this->loggedUser = (null !== $session->get("anosiaUser")) ?: null;
    }
}
