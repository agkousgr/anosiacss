<?php

namespace App\Controller;


use App\Entity\Checkout;
use App\Entity\OrdersWebId;
use App\Form\Type\{
    CheckoutStep1Type,
    CheckoutStep2Type,
    CheckoutStep3Type,
    CheckoutStep4Type
};
use App\Service\{CheckoutService, PireausRedirection, UserAccountService, PaypalService};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class CheckoutController extends MainController
{
    public function checkout(int $step, Request $request, CheckoutService $checkoutService, UserAccountService $userAccountService, EntityManagerInterface $em, PaypalService $paypalService, PireausRedirection $pireausRedirection)
    {
        try {
            $addresses = array();
            $userExist = false;
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
            if (null !== $this->loggedUser) {
                $addresses = $userAccountService->getAddresses($this->loggedClientId);
            }
            dump($checkout);
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
                if (null === $this->loggedUser) {
                    $userExist = $userAccountService->checkIfUserExist($checkout->getEmail());
                    if ($userExist) {
                        $curStep = 1;
                        $this->addFlash(
                            'notice',
                            'Υπάρχει ήδη χρήστης με το email που εισάγατε. Αν έχετε ήδη λογαριασμό κάντε login!'
                        );
                    }
                }
            } elseif ($step2Form->isSubmitted() && $step2Form->isValid()) {
                if ($this->session->get("addAddress")) {
                    $userAccountService->updateUserInfo($checkout);
                    $this->session->remove('addAddress');
                }
                $curStep = 3;
            } elseif ($step3Form->isSubmitted() && $step3Form->isValid()) {
                if ($step3Form->get('shippingType')->getData() === '1000') {
                    $checkout->setShippingCost(2.00);
                }
                $curStep = 4;
            }

            if ($step4Form->isSubmitted() && $step4Form->isValid()) {
                if (null === $this->loggedUser && 'Success' !== $createUserResult = $userAccountService->createUser($checkout)) {
                    $curStep = 4;
                    $this->addFlash(
                        'notice',
                        'Παρουσιάστηκε σφάλμα κατά την ολοκλήρωση της παραγγελίας σας. Κωδικός σφάλματος "' . $createUserResult . '"! Αν δεν είναι η πρώτη φορά που βλέπετε αυτό το σφάλμα παρακαλούμε επικοινωνείστε μαζί μας.'
                    );
                } else {
                    $onlinePaymentError = false;
                    if ($step4Form->get('paymentType')->getData() === '1003') {
                        $checkout->setAntikatavoliCost(1.50);
                    } else {
                        $checkout->setAntikatavoliCost(0);
                    }
                    $curStep = 4;
                    $orderWebId = $em->getRepository(OrdersWebId::class)->find(1);
                    $checkout->setOrderNo($orderWebId->getOrderNumber() + 1);
                    if ($checkout->getPaymentType() === '1002') {
                        $paypalService->sendToPaypal($checkout);

//                        $onlinePaymentError = true;
//                        die();
                    } else if ($checkout->getPaymentType() === '1001') {
                        $checkout->setInstallments(0);
                        $pireausRedirection->submitOrderToPireaus($checkout);
                        if ($checkout->getPireausResultCode() !== "0") {
                            $this->addFlash(
                                'notice',
                                $checkout->getPireausResultDescription() . ' ' . $checkout->getPireausResultAction()
                            );
                            $orderCompleted = false;
                            $onlinePaymentError = true;
                        }
                    }
                    if ($onlinePaymentError === false) {
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
                                'totalWishlistItems' => $this->totalWishlistItems,
                                'cartItems' => $this->cartItems,
                                'orderCompleted' => $orderCompleted,
                                'loginUrl' => $this->loginUrl
                            ]));
                        } else {
                            $this->addFlash(
                                'notice',
                                'Ένα σφάλμα παρουσιάστηκε κατά την διαδικασία της παραγγελίας σας. Παρακαλούμε προσπαθήστε ξανά. Αν το πρόβλημα παραμείνει επικοινωνήστε μαζί μας!'
                            );
                            $orderCompleted = false;
                        }
                    }
                }
            }
            $this->session->set('curOrder', $checkout);
            return ($this->render('orders/checkout.html.twig', [
                'categories' => $this->categories,
                'popular' => $this->popular,
                'featured' => $this->featured,
                'cartItems' => $this->cartItems,
                'totalCartItems' => $this->totalCartItems,
                'totalWishlistItems' => $this->totalWishlistItems,
                'loggedUser' => $this->loggedUser,
                'userExist' => $userExist,
                'addresses' => $addresses,
                'checkout' => $checkout,
                'step1Form' => $step1Form->createView(),
                'step2Form' => $step2Form->createView(),
                'step3Form' => $step3Form->createView(),
                'step4Form' => $step4Form->createView(),
                'curStep' => $curStep,
                'loginUrl' => $this->loginUrl
            ]));
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function guestCheckout(int $step, Request $request, CheckoutService $checkoutService, UserAccountService $userAccountService, EntityManagerInterface $em, PaypalService $paypalService)
    {
        try {
            $addresses = array();
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
            if (null !== $this->loggedUser) {
                $addresses = $userAccountService->getAddresses($this->loggedClientId);
            }
            dump($checkout);
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
                if (null !== $this->loggedUser) {

                }
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
                } else {
                    $checkout->setAntikatavoliCost(0);
                }
                $curStep = 4;
                $orderWebId = $em->getRepository(OrdersWebId::class)->find(1);
                $checkout->setOrderNo($orderWebId->getOrderNumber() + 1);
                $orderResponse = $checkoutService->submitOrder($checkout, $this->cartItems);
                if ($orderResponse) {

                    if ($checkout->getPaymentType() === '1002') {
                        dump('zong');
                        $paypalService->sendToPaypal($checkout);
//                        die();
                    }

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
                        'totalWishlistItems' => $this->totalWishlistItems,
                        'cartItems' => $this->cartItems,
                        'orderCompleted' => $orderCompleted,
                        'loginUrl' => $this->loginUrl
                    ]));
                } else {
                    $this->addFlash(
                        'notice',
                        'Ένα σφάλμα παρουσιάστηκε κατά την διαδικασία της παραγγελίας σας. Παρακαλούμε προσπαθήστε ξανά. Αν το πρόβλημα παραμείνει επικοινωνήστε μαζί μας!'
                    );
                    $orderCompleted = false;
                }
            }
            dump($addresses);
            return ($this->render('orders/checkout.html.twig', [
                'categories' => $this->categories,
                'popular' => $this->popular,
                'featured' => $this->featured,
                'cartItems' => $this->cartItems,
                'totalCartItems' => $this->totalCartItems,
                'totalWishlistItems' => $this->totalWishlistItems,
                'loggedUser' => $this->loggedUser,
                'addresses' => $addresses,
                'step1Form' => $step1Form->createView(),
                'step2Form' => $step2Form->createView(),
                'step3Form' => $step3Form->createView(),
                'step4Form' => $step4Form->createView(),
                'curStep' => $curStep,
                'loginUrl' => $this->loginUrl
            ]));
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}