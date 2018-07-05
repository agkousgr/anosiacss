<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 5/7/2018
 * Time: 12:28 πμ
 */

namespace App\Controller;

use App\Entity\Cart;
use App\Service\{
    CartService, CategoryService, ProductService, SoftoneLogin
};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CheckoutController extends AbstractController
{
    public function checkout(SoftoneLogin $softoneLogin, CategoryService $categoryService, SessionInterface $session, CartService $cart, EntityManagerInterface $em, ProductService $productService)
    {
        $softoneLogin->login();
        $categories = $categoryService->getCategories();
        $cartIds = '';
        if (null === $session->get('username')) {
            $cartArr = $em->getRepository(Cart::class)->getCartBySession($session->getId());
        } else {
            $cartArr = $em->getRepository(Cart::class)->getCartBySession($session->getId());
        }
        if ($cartArr) {
            foreach ($cartArr as $key => $val) {
                $cartIds .= $val->getProductId() . ',';
            }
            $cartIds = substr($cartIds, 0, -1);
        }
        $cartItems = ($cartIds) ? $cart->getCartItems($cartIds, $cartArr) : '';
        $popular = $productService->getCategoryItems(1022, $session->get("authID"));
        $loggedUser = (null !== $session->get("anosiaUser")) ?: null;
        return ($this->render('orders/checkout.html.twig', [
            'categories' => $categories,
            'cartItems' => $cartItems,
            'popular' => $popular,
            'loggedUser' => $loggedUser
        ]));
    }
}