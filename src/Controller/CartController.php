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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartController extends MainController
{
    public function viewCart(CartService $cartService)
    {
        try {
            $totalCartPrice = 0;
            $cartProposals = [];
            if ($this->cartItems) {
                foreach ($this->cartItems as $item) {
                    $totalCartPrice += floatval($item['webPrice']);
                }
                $cartProposals = $cartService->getRelevantItems(39 - $totalCartPrice);
            }
            $couponDiscount = (!empty($this->session->get('couponDisc'))) ? $this->session->get('couponDisc') : 0;
            dump($couponDiscount);
            return ($this->render('orders/cart.html.twig', [
                'categories' => $this->categories,
                'cartItems' => $this->cartItems,
                'totalCartItems' => $this->totalCartItems,
                'totalWishlistItems' => $this->totalWishlistItems,
                'popular' => $this->popular,
                'proposals' => $cartProposals,
                'featured' => $this->featured,
                'loggedUser' => $this->loggedUser,
                'loggedName' => $this->loggedName,
                'hideCart' => true,
                'loginUrl' => $this->loginUrl,
                'totalCartPrice' => $totalCartPrice,
                'cartProposals' => $cartProposals,
                'couponDiscount' => $couponDiscount
            ]));
        } catch (\Exception $e) {
            throw $e;
            //throw $this->createNotFoundException('The resource you are looking for could not be found.');
        }
    }

    public function updateCart(Request $request, EntityManagerInterface $em)
    {
        try {
            $quantityArr = $request->request->get("quantity");
            if (null !== $quantityArr) {
//            $cart = new Cart();
                foreach ($quantityArr as $key => $val) {
                    $cartItem = $em->getRepository(Cart::class)->find($key);
                    $cartItem->setQuantity($val);
                    $em->persist($cartItem);
                }
                $em->flush();
                $this->addFlash(
                    'success',
                    'Το καλάθι σας ενημερώθηκε με επιτυχία.'
                );
            }
            return $this->redirectToRoute('cart_view');
        } catch (\Exception $e) {
            throw $e;
            //throw $this->createNotFoundException('The resource you are looking for could not be found.');
        }
    }

    public function deleteCartItem(EntityManagerInterface $em, int $id)
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