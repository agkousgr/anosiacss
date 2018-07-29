<?php

namespace App\Controller;


use App\Entity\{Checkout, Address};
use App\Form\Type\{
    CheckoutStep1Type,
    CheckoutStep2Type,
    CheckoutStep3Type,
    CheckoutStep4Type
};
use App\Service\CheckoutService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class CheckoutController extends MainController
{
    public function checkout(int $step, Request $request, CheckoutService $checkoutService, EntityManagerInterface $em)
    {
        try {
            $userData = '';
            $curStep = ($request->request->get('currentStep')) ?: $step;
            if (null !== $this->loggedUser) {
                $checkout = new Checkout();
                $address = new Address();
                $orderData = $checkoutService->getUserInfo($checkout, $address);
                $this->session->set('curOrder', $orderData);
                if ($this->session->has('curOrder') === false) {
                } else {
//                    dump($this->session->get('curOrder')->replace('firstname', 'zong'));
                    $orderData = $this->session->get('curOrder');
                }
                $step1Form = $this->createForm(CheckoutStep1Type::class, $checkout, [
                    'loggedUser' => $this->loggedUser
                ]);
                $step1Form->handleRequest($request);
                $step2Form = $this->createForm(CheckoutStep2Type::class, $checkout);
                $step2Form->handleRequest($request);
                $step3Form = $this->createForm(CheckoutStep3Type::class, $checkout);
                $step3Form->handleRequest($request);
                $step4Form = $this->createForm(CheckoutStep4Type::class, $checkout);
                $step4Form->handleRequest($request);
                if ($step1Form->isSubmitted() && $step1Form->isValid()) {
                    array_push($orderData, [
                        'name' => $step1Form->get("firstname")->getData() . " " . $step1Form->get("lastname")->getData()
                    ]);
//                    $this->session->set('curOrder', $orderData);
                    $curStep = 2;
                }
                if ($step2Form->isSubmitted() && $step2Form->isValid()) {

                    $curStep = 3;
                }
                if ($step3Form->isSubmitted() && $step3Form->isValid()) {

                    $curStep = 4;
                }
                if ($step4Form->isSubmitted() && $step4Form->isValid()) {

                }

                $checkoutService->initializeOrder($checkout);

                dump($this->session->get('curOrder'));

                return ($this->render('orders/checkout.html.twig', [
                    'categories' => $this->categories,
                    'popular' => $this->popular,
                    'featured' => $this->featured,
                    'cartItems' => $this->cartItems,
                    'totalCartItems' => $this->totalCartItems,
                    'loggedUser' => $this->loggedUser,
                    'orderData' => $orderData,
                    'step1Form' => $step1Form->createView(),
                    'step2Form' => $step2Form->createView(),
                    'step3Form' => $step3Form->createView(),
                    'step4Form' => $step4Form->createView(),
                    'curStep' => $curStep
                ]));
            } else {
                throw $this->createNotFoundException('Δεν έχετε πρόσβαση σε αυτή τη σελίδα. Συνδεθείτε και προσπαθήστε ξανά');
//                return $this->redirectToRoute('404');
            }
//            $registerForm = $this->createForm(UserRegistrationType::class);
//
//            $registerForm->handleRequest($request);
//            if ($registerForm->isSubmitted() && $registerForm->isValid()) {
//                $newUser = $userAccountService->createUser($registerForm->getData());
//            }
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}