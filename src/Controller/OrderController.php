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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class OrderController extends AbstractController
{
    public function addToCart(Request $request, EntityManagerInterface $em, Cart $cart, SessionInterface $session)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $prId = $request->query->getInt('id') ?: $request->request->getInt('id');
                $quantity = $request->query->getInt('quantity') ?: 1;

                $cart->setQuantity($quantity);
                $cart->setProductId($prId);
                $cart->setSessionId($session->getId());
                if (null !== $session->get('username')) ($cart->setUsername($session->get('username')));

                if (null !== $prId) {
                    return $this->render('orders/add_to_cart.html.twig', [
                        'id' => $prId
                    ]);
                }
            } catch (\Exception $e) {
                throw $e;
                //throw $this->createNotFoundException('The resource you are looking for could not be found.');
            }
        } else {
            throw $this->createNotFoundException('The resource you are looking for could not be found.');
        }


    }
}