<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\PaypalTransaction;
use Beelab\PaypalBundle\Paypal\Service as PaypalService;

class PaypalController extends AbstractController
{
    public function return(Request $request, EntityManagerInterface $em, PaypalService $paypalService)
    {
        $token = $request->query->get('token');
        $transaction = $em->getRepository(PaypalTransaction::class)->findOneByToken($token);
        if (null === $transaction)
            throw $this->createNotFoundException(sprintf('Transaction with token %s not found.', $token));

        $paypalService->setTransaction($transaction)->complete();
        $em->flush();
        if (!$transaction->isOk()) {
            return $this->redirectToRoute('checkout');
        }

        return $this->redirectToRoute('checkout');
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
