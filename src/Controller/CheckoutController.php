<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 5/7/2018
 * Time: 12:28 πμ
 */

namespace App\Controller;

use App\Form\Type\UserRegistrationType;
use App\Service\UserAccountService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class CheckoutController extends MainController
{
    public function checkout(Request $request, UserAccountService $userAccountService, EntityManagerInterface $em)
    {
        $registerForm = $this->createForm(UserRegistrationType::class);

        $registerForm->handleRequest($request);
        if ($registerForm->isSubmitted() && $registerForm->isValid()) {
            $newUser = $userAccountService->createUser($registerForm->getData());
        }
        return ($this->render('orders/checkout.html.twig', [
            'categories' => $this->categories,
            'popular' => $this->popular,
            'featured' => $this->featured,
            'cartItems' => $this->cartItems,
            'totalCartItems' => $this->totalCartItems,
            'loggedUser' => $this->loggedUser,
            'registerForm' => $registerForm->createView()
        ]));
    }
}