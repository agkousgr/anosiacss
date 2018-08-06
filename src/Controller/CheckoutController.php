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

            if ($this->totalCartItems === 0) {
                return $this->redirectToRoute('cart_view');
            }
            $curStep = ($request->request->get('currentStep')) ?: $step;
            if ($this->session->has('curOrder') === false) {
                $checkout = new Checkout();
                if (null !== $this->loggedUser) {
                    $checkoutService->getUserInfo($checkout);
                }
                $this->session->set('curOrder', $checkout);
            } else {
                $checkout = $this->session->get('curOrder');
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
                $curStep = 2;
            } elseif ($step2Form->isSubmitted() && $step2Form->isValid()) {
                $curStep = 3;
            } elseif ($step3Form->isSubmitted() && $step3Form->isValid()) {
                if ($step3Form->get('shippingType')->getData() === '1000') {
                    $checkout->setShippingCost(2.00);
                }
                $curStep = 4;
            }
            $this->session->set('curOrder', $checkout);
            if ($step4Form->isSubmitted() && $step4Form->isValid()) {
                if ($step4Form->get('paymentType')->getData() === '1003') {
                    $checkout->setAntikatavoliCost(1.50);
                }else{
                    $checkout->setAntikatavoliCost(0);
                }
                $curStep = 4;
                $orderResponse = $checkoutService->submitOrder($checkout, $this->cartItems);
                if ($orderResponse) {
                    $this->addFlash(
                        'success',
                        'Η παραγγελία σας ολοκληρώθηκε με επιτυχία. Ένα αντίγραφο έχει αποσταλεί στο email σας ' . $checkout->getEmail() . '. Ευχαριστούμε που μας προτιμήσατε για τις αγορές σας!'
                    );
                    $checkoutService->sendOrderConfirmationEmail($checkout);
//                    $checkoutService->emptyCart($this->cartItems, $em);
                    $orderCompleted = true;
                    // CLEAR curOrder SESSION
                    return ($this->render('orders/order_completed.html.twig', [
                        'categories' => $this->categories,
                        'popular' => $this->popular,
                        'featured' => $this->featured,
                        'checkout' => $checkout,
                        'loggedUser' => $this->loggedUser,
                        'totalCartItems' => $this->totalCartItems,
                        'cartItems' => $this->cartItems,
                        'orderCompleted' => $orderCompleted
                    ]));
                } else {
                    $this->addFlash(
                        'notice',
                        'Ένα σφάλμα παρουσιάστηκε κατά την διαδικασία της παραγγελίας σας. Παρακαλούμε προσπαθήστε ξανά. Αν το πρόβλημα παραμείνει επικοινωνήστε μαζί μας!'
                    );
                    $orderCompleted = false;
                }
            }
            return ($this->render('orders/checkout.html.twig', [
                'categories' => $this->categories,
                'popular' => $this->popular,
                'featured' => $this->featured,
                'cartItems' => $this->cartItems,
                'totalCartItems' => $this->totalCartItems,
                'loggedUser' => $this->loggedUser,
                'step1Form' => $step1Form->createView(),
                'step2Form' => $step2Form->createView(),
                'step3Form' => $step3Form->createView(),
                'step4Form' => $step4Form->createView(),
                'curStep' => $curStep
            ]));
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}