<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Entity\Checkout;

/**
 * Class CheckoutCompleted
 */
class CheckoutCompleted
{
    /**
     * @var CheckoutService
     */
    private $checkoutService;

    /**
     * @var NewsletterService
     */
    private $newsletterService;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * Constructor.
     *
     * @param \App\Service\CheckoutService $checkoutService
     * @param \App\Service\NewsletterService $newsletterService
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     */
    public function __construct(
        CheckoutService $checkoutService, NewsletterService $newsletterService, SessionInterface $session, RequestStack $requestStack
    )
    {
        $this->checkoutService = $checkoutService;
        $this->newsletterService = $newsletterService;
        $this->session = $session;
        $this->requestStack = $requestStack;
    }


    public function handleSuccessfulPayment(array $cartItems): Checkout
    {
        $checkout = $this->session->get('curOrder');
        $orderResponse = $this->checkoutService->submitOrder($checkout, $cartItems);
        if ($orderResponse) {
            $this->checkoutService->sendOrderConfirmationEmail($checkout, $cartItems);
            $this->checkoutService->emptyCart($cartItems);
            $this->saveNewsletter($checkout);
            // CLEAR curOrder SESSION
            $this->session->remove('curOrder');
            $this->session->remove('couponDisc');
            $this->session->remove('couponDiscPerc');
            $this->session->remove('couponName');
            $this->session->remove('couponId');

        }
        return $checkout;
    }

    public function handleFailedPayment()
    {

    }

    private function saveNewsletter(Checkout $checkout)
    {
        if ($checkout->isNewsletter() && $checkout->getNewsletterId() === '') {
            $request = $this->requestStack->getCurrentRequest();
            $date = (new \DateTime())->format('Y-m-d H:i:s');
            $referrer = 'USER AGENT: ' . $request->headers->get('User-Agent') . ' REFERRER: ' . $request->headers->get('referer') . ' DATE: ' . $date;
            $this->newsletterService->getNewsletter(
                $checkout->getFirstName() . ' ' . $checkout->getLastName(),
                $checkout->getEmail(),
                $referrer,
                $checkout->getNewsLetterGender(),
                $checkout->getNewsLetterAge()
            );
        }
    }
}
