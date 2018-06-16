<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 23/5/2018
 * Time: 1:14 πμ
 */

namespace App\Controller;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends AbstractController
{
    public function addToCart(Request $request, EntityManagerInterface $manager)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $prId = $request->query->getInt('id') ?: $request->request->getInt('id');

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