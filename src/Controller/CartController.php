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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartController extends AbstractController
{
    public function viewCart(SessionInterface $session, CartService $cart)
    {
        try {
            if (null !== $session->get('username')) {
                $cart->setUsername($session->get('username'));
            }

            return ($this->render('partials/top_cart.html.twig'));
        } catch (\Exception $e) {
            throw $e;
            //throw $this->createNotFoundException('The resource you are looking for could not be found.');
        }
    }

    public function addToCart(Request $request, EntityManagerInterface $em, SessionInterface $session)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                dump($request);
                $prId = $request->query->getInt('id');
                dump($prId);
                $quantity = $request->request->getInt('quantity') ?: 1;
                $cart = new Cart();
                $cart->setQuantity($quantity);
                $cart->setProductId(29076);
                $cart->setSessionId($session->getId());
                $date = new \DateTime("now");
                $cart->setCreatedAt($date);
                $cart->setUpdatedAt($date);
                if (null !== $session->get('username')) {
                    $cart->setUsername($session->get('username'));
                }
                dump($cart);
                if (null !== $prId) {
                    $em->persist($cart);
                    $em->flush();

                    return ($this->render('partials/top_cart.html.twig'));
                }
                return $this->json(['success' => false]);
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
            if ($cartArr) {
                foreach ($cartArr as $key => $val) {
                    $cartIds .= $val->getProductId() . ',';
                }
                $cartIds = substr($cartIds, 0, -1);
            }
        }

        $cartItems = ($cartIds) ? $cart->getCartItems($cartIds) : '';
        dump('cart items', $cartItems);
        return ($this->render('partials/top_cart.html.twig', [
            'cartItems' => $cartItems
        ]));
    }
}