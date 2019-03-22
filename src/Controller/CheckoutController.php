<?php

namespace App\Controller;

use App\Entity\{Checkout, OrdersWebId};
use App\Form\Type\{
    CheckoutStep1Type,
    CheckoutStep2Type,
    CheckoutStep3Type,
    CheckoutStep4Type
};
use App\Service\{CheckoutService, PireausRedirection, UserAccountService, PaypalService, NewsletterService};
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

class CheckoutController extends MainController
{
    public function checkout(Request $request, CheckoutService $checkoutService, UserAccountService $userAccountService, EntityManagerInterface $em, PaypalService $paypalService, PireausRedirection $pireausRedirection)
    {
        try {
            $addresses = array();
            $userExist = false;
            if ($this->totalCartItems === 0) {
                return $this->redirectToRoute('cart_view');
            }
            $curStep = ($request->request->get('currentStep')) ?: 1;
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
            if ($this->session->has('couponDisc') === true) {
                $checkout->setCouponDisc($this->session->get('couponDisc'));
                $checkout->setCouponDiscPerc($this->session->get('couponDiscPerc'));
                $checkout->setCouponName($this->session->get('couponName'));
                $checkout->setCouponId($this->session->get('couponId'));
            }
            dump($checkout);
            $step1Form = $this->createForm(CheckoutStep1Type::class, $checkout, [
                'loggedUser' => $this->loggedUser
            ]);
            $step1Form->handleRequest($request);
            $step2Form = $this->createForm(CheckoutStep2Type::class, $checkout);
            $step2Form->handleRequest($request);
//            $step3Form = $this->createForm(CheckoutStep3Type::class, $checkout);
//            $step3Form->handleRequest($request);
//            $step4Form = $this->createForm(CheckoutStep4Type::class, $checkout);
//            $step4Form->handleRequest($request);
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
                if ($this->session->get("addAddress")) {
                    $userAccountService->updateUserInfo($checkout);
                    $this->session->remove('addAddress');
                }
//            } elseif ($step2Form->isSubmitted() && $step2Form->isValid()) {
//                if ($this->session->get("addAddress")) {
//                    $userAccountService->updateUserInfo($checkout);
//                    $this->session->remove('addAddress');
//                }
//                $curStep = 3;
            }
//            elseif ($step3Form->isSubmitted() && $step3Form->isValid()) {
//                if ($step3Form->get('shippingType')->getData() === '1000') {
//                    $checkout->setShippingCost(2.00);
//                }
//                $curStep = 4;
//            }

            if ($step2Form->isSubmitted() && $step2Form->isValid()) {
//                if (null === $this->loggedUser && 'Success' !== $createUserResult = $userAccountService->createUser($checkout)) {
//                    $curStep = 4;
//                    $this->addFlash(
//                        'notice',
//                        'Παρουσιάστηκε σφάλμα κατά την ολοκλήρωση της παραγγελίας σας. Κωδικός σφάλματος "' . $createUserResult . '"! Αν δεν είναι η πρώτη φορά που βλέπετε αυτό το σφάλμα παρακαλούμε επικοινωνείστε μαζί μας.'
//                    );
//                } else {
                $cartCost = $checkoutService->calculateCartCost($this->cartItems);
                if ($step2Form->get('paymentType')->getData() === '1007' && $cartCost < 39) {
                    $checkout->setAntikatavoliCost(1.50);
                } else {
                    $checkout->setAntikatavoliCost(0);
                }
                $curStep = 4;
                return $this->json(['success' => true]);
            }
//            }
            $this->session->set('curOrder', $checkout);
            $bank_config['AcquirerId'] = 14;
            $bank_config['MerchantId'] = 2137477493;
            $bank_config['PosId'] = 2141384532;
            $bank_config['User'] = 'AN895032';
            $bank_config['LanguageCode'] = 'el-GR';
            return ($this->render('orders/checkout.html.twig', [
                'categories' => $this->categories,
                'topSellers' => $this->topSellers,
                'popular' => $this->popular,
                'featured' => $this->featured,
                'cartItems' => $this->cartItems,
                'bankConfig' => $bank_config,
                'totalCartItems' => $this->totalCartItems,
                'totalWishlistItems' => $this->totalWishlistItems,
                'loggedUser' => $this->loggedUser,
                'userExist' => $userExist,
                'addresses' => $addresses,
                'checkout' => $checkout,
                'step1Form' => $step1Form->createView(),
                'step2Form' => $step2Form->createView(),
//                'step3Form' => $step3Form->createView(),
//                'step4Form' => $step4Form->createView(),
                'curStep' => $curStep,
                'loginUrl' => $this->loginUrl
            ]));
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function completeCheckout(
        Request $request, CheckoutService $checkoutService, UserAccountService $userAccountService, EntityManagerInterface $em,
        PaypalService $paypalService, PireausRedirection $pireausRedirection, NewsletterService $newsletterService
    )
    {
        $onlinePaymentError = false;
        /** @var \App\Entity\Checkout */
        $checkout = $this->session->get('curOrder');
        $cartCost = $checkoutService->calculateCartCost($this->cartItems);
        $checkout->setTotalOrderCost($cartCost + $checkout->getAntikatavoliCost() + $checkout->getShippingCost());
        $orderWebId = $em->getRepository(OrdersWebId::class)->find(1);
        $checkout->setOrderNo($orderWebId->getOrderNumber() + 1);
        if ($checkout->getPaymentType() === '1006') {
            $paypalService->sendToPaypal($checkout);

//                        $onlinePaymentError = true;
//                        die();
        } else if ($checkout->getPaymentType() === '1001') {
            $checkout->setInstallments(0);
            $pireausRedirection->submitOrderToPireaus($checkout);
            die();
            if ($checkout->getPireausResultCode() !== "0") {
                $this->addFlash(
                    'notice',
                    $checkout->getPireausResultDescription() . ' ' . $checkout->getPireausResultAction()
                );
                $orderCompleted = false;
                $onlinePaymentError = true;
            } else {
                $this->session->set('curOrder', $checkout);
                $bank_config['AcquirerId'] = 14;
                $bank_config['MerchantId'] = 2137477493;
                $bank_config['PosId'] = 2141384532;
                $bank_config['User'] = 'AN895032';
                $bank_config['LanguageCode'] = 'el-GR';

                return $this->render('orders/pireaus_iframe.html.twig', [
                    'checkout' => $checkout,
                    'bankConfig' => $bank_config,
                    'categories' => $this->categories,
                    'topSellers' => $this->topSellers,
                    'popular' => $this->popular,
                    'featured' => $this->featured,
                    'loggedUser' => $this->loggedUser,
                    'totalCartItems' => $this->totalCartItems,
                    'totalWishlistItems' => $this->totalWishlistItems,
                    'cartItems' => $this->cartItems,
                    'loginUrl' => $this->loginUrl
                ]);
            }
        }
        if ($onlinePaymentError === false) {
            $orderResponse = $checkoutService->submitOrder($checkout, $this->cartItems);
            if ($orderResponse) {
                if ($checkout->isNewsletter()) {
                    $date = date('Y-m-d H:i:s');
                    $referrer = 'USER AGENT: ' . $request->headers->get('User-Agent') . ' REFERRER: ' . $request->headers->get('referer') . ' DATE: ' . $date;
                    $newsletterService->getNewsletter(
                        $checkout->getFirstName() . ' ' . $checkout->getLastName(),
                        $checkout->getEmail(),
                        $referrer,
                        $checkout->getNewsLetterGender(),
                        $checkout->getNewsLetterAge()
                    );
                }

                $this->addFlash(
                    'success',
                    'Η παραγγελία σας ολοκληρώθηκε με επιτυχία. Ένα αντίγραφο έχει αποσταλεί στο email σας ' . $checkout->getEmail() . '. Ευχαριστούμε που μας προτιμήσατε για τις αγορές σας!'
                );
//                $orderWebId->setOrderNumber($checkout->getOrderNo() + 1);
                $em->flush();
                $checkoutService->sendOrderConfirmationEmail($checkout, $this->cartItems);
                $checkoutService->emptyCart($this->cartItems, $em);
                $orderCompleted = true;
                // CLEAR curOrder SESSION
                $this->session->remove('curOrder');
                $this->session->remove('couponDisc');
                $this->session->remove('couponDiscPerc');
                $this->session->remove('couponName');
                $this->session->remove('couponId');

                return ($this->render('orders/order_completed.html.twig', [
                    'categories' => $this->categories,
                    'topSellers' => $this->topSellers,
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

    public function getPireausTicket(CheckoutService $checkoutService, EntityManagerInterface $em, PireausRedirection $pireausRedirection)
    {
        $checkout = $this->session->get('curOrder');
        $cartCost = $checkoutService->calculateCartCost($this->cartItems);

        $orderWebId = $em->getRepository(OrdersWebId::class)->find(1);
        $checkout->setOrderNo($orderWebId->getOrderNumber() + 1);
        $checkout->setTotalOrderCost($cartCost + $checkout->getAntikatavoliCost() + $checkout->getShippingCost());
        $checkout->setInstallments(0);
        $checkout = $pireausRedirection->submitOrderToPireaus($checkout);
        $this->session->set('curOrder', $checkout);
        dump($checkout);
        $bank_config['AcquirerId'] = 14;
        $bank_config['MerchantId'] = 2137477493;
        $bank_config['PosId'] = 2141384532;
        $bank_config['User'] = 'AN895032';
        $bank_config['LanguageCode'] = 'el-GR';

        return $this->json(['success' => true, 'checkout' => $checkout, 'bank_config' => $bank_config]);
    }
}