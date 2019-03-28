<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\PaypalTransaction;
use Beelab\PaypalBundle\Paypal\Service as PaypalService;
use App\Service\CheckoutCompleted;

class PaypalController extends MainController
{
    public function return(Request $request, EntityManagerInterface $em, PaypalService $paypalService, CheckoutCompleted $checkoutCompleted)
    {
        $token = $request->query->get('token');
        $transaction = $em->getRepository(PaypalTransaction::class)->findOneByToken($token);
        if (null === $transaction)
            throw $this->createNotFoundException(sprintf('Transaction with token %s not found.', $token));

        $paypalService->setTransaction($transaction)->complete();
        $em->flush();

        if (!$transaction->isOk()) {
            $checkoutCompleted->handleFailedPayment();

            return $this->redirectToRoute('checkout');
        }

        // TODO: Change return route, if SoftOne has not received data
        return $checkoutCompleted->handleSuccessfulPayment($this->cartItems) ?
            $this->redirectToRoute('checkout') :
            $this->redirectToRoute('checkout');
    }

    public function cancel(Request $request, EntityManagerInterface $em)
    {
        $token = $request->query->get('token');
        $transaction = $em->getRepository(PaypalTransaction::class)->findOneByToken($token);
        if (null === $transaction)
            throw $this->createNotFoundException(sprintf('Transaction with token %s not found.', $token));

        $transaction->cancel(null);
        $em->flush();

        return $this->redirectToRoute('checkout');
    }
}
