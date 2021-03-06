<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Product;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AjaxCartController extends AbstractController
{
    public function loadTopCart(Request $request, EntityManagerInterface $em, CartService $cartService, SessionInterface $session)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $cartIds = '';
                $totalCartItems = 0;
                if (null !== $session->get('anosiaUser')) {
                    $cartArr = $em->getRepository(Cart::class)->getCartByUser($session->get('anosiaUser'));
                } else {
                    $cartArr = $em->getRepository(Cart::class)->getCartBySession($session->getId());
                }

                if ($cartArr) {
                    foreach ($cartArr as $item) {
                        $cartIds .= $item->getProduct()->getId() . ',';
//                        $totalCartItems += 1 * $val->getQuantity();
                        $totalCartItems += 1;
                    }
                    $cartIds = substr($cartIds, 0, -1);
                }

                $cartItems = ($cartIds) ? $cartService->getCartItems($cartIds, $cartArr, $totalCartItems) : '';
                return $this->render('partials/top_cart.html.twig', [
                    'cartItems' => $cartItems,
                    'totalCartItems' => $totalCartItems
                ]);
            } catch (\Exception $e) {
                throw $e;
                //throw $this->createNotFoundException('The resource you are looking for could not be found.');
            }
        } else {
            throw $this->createNotFoundException('The resource you are looking for could not be found.');
        }
    }

    public function addToCart(Request $request, EntityManagerInterface $em, SessionInterface $session)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $id = $request->request->get('id');
                $productName = $request->request->get('name');
                $quantity = $request->request->get('quantity') ?: 1;
                $itemInCart = (int)$em->getRepository(Cart::class)->checkIfProductExists($session->getId(), $session->get('anosiaUser'), $id);

                if ($itemInCart === 0) {
                    $product = $em->getRepository(Product::class)->find($id);
                    $cart = new Cart();
                    $cart->setQuantity($quantity);
                    $cart->setProduct($product);
                    $cart->setSessionId($session->getId());
                    if (null !== $session->get('anosiaUser')) {
                        $cart->setUsername($session->get('anosiaUser'));
                    }

                    if (null !== $id) {
                        $em->persist($cart);
                        $em->flush();
//                        return $this->redirectToRoute('load_top_cart');

                        return $this->json([
                            'success' => true,
                            'exist' => false,
                            'totalCartItems' => $em->getRepository(Cart::class)->countCartItems($session->getId(), $session->get('anosiaUser')),
                            'prName' => $productName,

                        ]);
                    }
                } else {
                    return $this->json([
                        'success' => true,
                        'exist' => true,
                        'prName' => $productName,
                    ]);
                }
//                return $this->redirectToRoute('load_top_cart');
                return $this->json(['success' => false]);
            } catch (\Exception $e) {
                throw $e;
                //throw $this->createNotFoundException('The resource you are looking for could not be found.');
            }
        } else {
            throw $this->createNotFoundException('The resource you are looking for could not be found.');
        }
    }

    public function deleteTopCartItem(Request $request, EntityManagerInterface $em, SessionInterface $session)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $id = $request->request->get('id');
                $productName = $request->request->get('name');
                if (!$id) {
                    throw $this->createNotFoundException(
                        'No product found for id ' . $id
                    );
                }
                $cartItem = $em->getRepository(Cart::class)->find($id);

                // Add code for checking that sessionId or Username has access to specific cartId
                // Add here
                // End code
                if (null !== $id) {
                    $em->remove($cartItem);
                    $em->flush();
                    return $this->json([
                        'success' => true,
                        'totalCartItems' => $em->getRepository(Cart::class)->countCartItems($session->getId(), $session->get('anosiaUser')),
                        'prName' => $productName,

                    ]);
                }
                return $this->json(['success' => false]);
            } catch (\Exception $e) {
                return $this->json(['success' => false]);
                throw $e;
                //throw $this->createNotFoundException('The resource you are looking for could not be found.');
            }
        } else {
            throw $this->createNotFoundException('The resource you are looking for could not be found.');
        }
    }

    public function checkCoupon(Request $request, CartService $cartService)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $couponResult = $cartService->checkCoupon(-1, $request->request->get('coupon'));

                if ($couponResult) {
                    return $this->json(['success' => true]);
                } else {
                    return $this->json(['success' => false]);
                }
            } catch (\Exception $e) {
                return $this->json(['success' => false]);
                throw $e;
                //throw $this->createNotFoundException('The resource you are looking for could not be found.');
            }
        } else {
            throw $this->createNotFoundException('The resource you are looking for could not be found.');
        }
    }

    public function removeCoupon(Request $request, SessionInterface $session)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $session->remove('couponDisc');
                $session->remove('couponName');
                $session->remove('couponId');

                return $this->json(['success' => true]);
            } catch (\Exception $e) {
                return $this->json(['success' => false]);
                throw $e;
                //throw $this->createNotFoundException('The resource you are looking for could not be found.');
            }
        } else {
            throw $this->createNotFoundException('The resource you are looking for could not be found.');
        }
    }

}