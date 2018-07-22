<?php

namespace App\Controller;


use App\Entity\{WebUser, Address};
use App\Form\Type\CheckoutUserType;
use App\Form\Type\UserRegistrationType;
use App\Service\UserAccountService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class CheckoutController extends MainController
{
    public function checkout(Request $request, UserAccountService $userAccountService, EntityManagerInterface $em)
    {
        try {
            $userData = '';
            if (null !== $this->loggedUser) {
                $user = new WebUser();
                $address = new Address();
                $userData = $userAccountService->getUserInfo($this->loggedUser, $user, $address);
                $checkoutForm = $this->createForm(CheckoutUserType::class, $user);

            }
//            $registerForm = $this->createForm(UserRegistrationType::class);
//
//            $registerForm->handleRequest($request);
//            if ($registerForm->isSubmitted() && $registerForm->isValid()) {
//                $newUser = $userAccountService->createUser($registerForm->getData());
//            }
            return ($this->render('orders/checkout.html.twig', [
                'categories' => $this->categories,
                'popular' => $this->popular,
                'featured' => $this->featured,
                'cartItems' => $this->cartItems,
                'totalCartItems' => $this->totalCartItems,
                'loggedUser' => $this->loggedUser,
                'userData' => $userData,
                'registerForm' => $checkoutForm->createView()
            ]));
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}