<?php

namespace App\Controller;

use App\Entity\{Checkout, OrdersWebId, PaypalTransaction};
use App\Form\Type\{CheckoutStep1Type, CheckoutStep2Type};
use App\Service\{CheckoutCompleted, CheckoutService, PireausRedirection, UserAccountService};
use Beelab\PaypalBundle\Paypal\Service as PaypalService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CheckoutController extends MainController
{
    public function checkout(Request $request, CheckoutService $checkoutService, UserAccountService $userAccountService)
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
            }

            if ($step2Form->isSubmitted() && $step2Form->isValid()) {
                $cartCost = $checkoutService->calculateCartCost($this->cartItems);
                if ($step2Form->get('paymentType')->getData() === '1007' && $cartCost < 39) {
                    $checkout->setAntikatavoliCost(1.50);
                } else {
                    $checkout->setAntikatavoliCost(0);
                }
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
                'curStep' => $curStep,
                'loginUrl' => $this->loginUrl
            ]));
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function completeCheckout(CheckoutService $checkoutService, EntityManagerInterface $em, PaypalService $paypalService, CheckoutCompleted $checkoutCompleted)
    {
        try {
            /** @var \App\Entity\Checkout */
            $checkout = $this->session->get('curOrder');
            $cartCost = $checkoutService->calculateCartCost($this->cartItems);
            $checkout->setTotalOrderCost($cartCost + $checkout->getAntikatavoliCost() + $checkout->getShippingCost());//        $orderCode = $em->getRepository(OrdersWebId::class)->find(1);
            //        $checkout->setOrderNo(intval(str_replace('ORDER', $orderCode->getOrderNumber())) + 1);
            // Check if necessary at this point
            $em->flush();
            if ($checkout->getPaymentType() === '1006') {
                $amount = $checkout->getTotalOrderCost();
                $transaction = new PaypalTransaction($amount);
                try {
                    $response = $paypalService->setTransaction($transaction)->start();
                    $em->persist($transaction);
                    $em->flush();

                    return $this->redirect($response->getRedirectUrl());
                } catch (\Exception $e) {
                    throw new HttpException(503, 'Payment error', $e);
                }
            }else{
                $cartItems = $this->cartItems;
                $checkoutCompleted->handleSuccessfulPayment($cartItems);
                return $this->render('orders/order_completed.html.twig', [
                    'categories' => $this->categories,
                    'popular' => $this->popular,
                    'featured' => $this->featured,
                    'topSellers' => $this->topSellers,
                    'checkout' => $checkout,
                    'loggedUser' => $this->loggedUser,
                    'totalCartItems' => $this->totalCartItems,
                    'totalWishlistItems' => $this->totalWishlistItems,
                    'cartItems' => $cartItems,
                    'orderCompleted' => true,
                    'loginUrl' => $this->loginUrl
                ]);
            }
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getPireausTicket(Request $request, CheckoutService $checkoutService, PireausRedirection $pireausRedirection)
    {
        $installments = ($request->request->get('installments') && $request->request->get('installments') <= 6) ? $request->request->get('installments') : 0;
        dump($installments);
        $checkout = $this->session->get('curOrder');
        $cartCost = $checkoutService->calculateCartCost($this->cartItems);

        $orderCode = ($this->loggedClientId) ? $this->loggedClientId . '-' . time() : random(100, 999) . '-' . time();
        $checkout->setOrderNo($orderCode);
        $checkout->setTotalOrderCost($cartCost + $checkout->getAntikatavoliCost() + $checkout->getShippingCost());
        $checkout->setInstallments($installments);
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