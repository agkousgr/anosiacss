<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 23/5/2018
 * Time: 1:14 πμ
 */

namespace App\Controller;


use App\Entity\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartController extends MainController
{
    public function viewCart(EntityManagerInterface $em)
    {
        try {

            return ($this->render('orders/cart.html.twig', [
                'categories' => $this->categories,
                'cartItems' => $this->cartItems,
                'totalCartItems' => $this->totalCartItems,
                'popular' => $this->popular,
                'featured' => $this->featured,
                'loggedUser' => $this->loggedUser,
                'hideCart' => true
            ]));
        } catch (\Exception $e) {
            throw $e;
            //throw $this->createNotFoundException('The resource you are looking for could not be found.');
        }
    }

}