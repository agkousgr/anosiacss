<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 23/5/2018
 * Time: 1:14 πμ
 */

namespace App\Controller;


use App\Entity\Cart;
use App\Service\CartService;
use App\Service\CategoryService;
use App\Service\ProductService;
use App\Service\SoftoneLogin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartController extends MainController
{
    public function viewCart(EntityManagerInterface $em)
    {
        try {
            $cartIds = '';
            if (null === $this->session->get('username')) {
                $cartArr = $em->getRepository(Cart::class)->getCartBySession($this->session->getId());
            } else {
                $cartArr = $em->getRepository(Cart::class)->getCartBySession($this->session->getId());
            }
            if ($cartArr) {
                foreach ($cartArr as $key => $val) {
                    $cartIds .= $val->getProductId() . ',';
                }
                $cartIds = substr($cartIds, 0, -1);
            }
            $cartItems = ($cartIds) ? $this->cart->getCartItems($cartIds, $cartArr) : '';

            return ($this->render('orders/cart.html.twig', [
                'categories' => $this->categories,
                'cartItems' => $cartItems,
                'popular' => $this->popular,
                'loggedUser' => $this->loggedUser
            ]));
        } catch (\Exception $e) {
            throw $e;
            //throw $this->createNotFoundException('The resource you are looking for could not be found.');
        }
    }

    public function addToCart(int $id, int $quantity, Request $request, EntityManagerInterface $em, SessionInterface $session)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $itemInCart = (int)$em->getRepository(Cart::class)->checkIfProductExists($session->getId(), $session->get('anosiaUser'), $id);
                dump($itemInCart);
                $quantity = ($quantity) ?: 1;
                if ($itemInCart === 0) {
                    $cart = new Cart();
                    $cart->setQuantity($quantity);
                    $cart->setProductId($id);
                    $cart->setSessionId($session->getId());
                    $date = new \DateTime("now");
//                $cart->setCreatedAt($date);
//                $cart->setUpdatedAt($date);
                    if (null !== $session->get('username')) {
                        $cart->setUsername($session->get('username'));
                    }
                    dump($cart);
                    if (null !== $id) {
                        $em->persist($cart);
                        $em->flush();
                        return $this->redirectToRoute('load_top_cart');
                    }
                }
                return $this->redirectToRoute('load_top_cart');
            } catch (\Exception $e) {
                throw $e;
                //throw $this->createNotFoundException('The resource you are looking for could not be found.');
            }
        } else {
            throw $this->createNotFoundException('The resource you are looking for could not be found.');
        }
    }

    public function loadTopCart(EntityManagerInterface $em, CartService $cart, SessionInterface $session)
    {
//        $cart = new CartService();
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
        return ($this->render('partials/top_cart.html.twig', [
            'cartItems' => $cartItems
        ]));
    }

    public function deleteCartItem(EntityManagerInterface $em, int $id, SessionInterface $session)
    {
        try {
            if (!$id) {
                throw $this->createNotFoundException(
                    'No product found for id ' . $id
                );
            }
            $cartItem = $em->getRepository(Cart::class)->find($id);
            // Add code for checking that sessionId or Username has access to specific cartId
            // Add here
            // End code

            $em->remove($cartItem);
            $em->flush();
            return $this->redirectToRoute('cart_view');
        } catch (\Exception $e) {
            throw $e;
            //throw $this->createNotFoundException('The resource you are looking for could not be found.');
        }
    }
}