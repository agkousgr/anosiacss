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
        $cartItems = $this->cartItems;
        $token = $request->query->get('token');
        $transaction = $em->getRepository(PaypalTransaction::class)->findOneByToken($token);
        if (null === $transaction)
            throw $this->createNotFoundException(sprintf('Transaction with token %s not found.', $token));

        $paypalService->setTransaction($transaction)->complete();
        $em->flush();

        if (!$transaction->isOk()) {
            $checkoutCompleted->handleFailedPayment();
            $this->addFlash('notice', 'H πληρωμή σας μέσω Paypal απέτυχε. Παρακαλώ δοκιμάστε ξανά ή αλλάξτε τρόπο πληρωμής');
            return $this->redirectToRoute('checkout');
        }
        $this->addFlash('success', 'Η συναλλαγή ολοκληρώθηκε με επιτυχία! Ένα αντίγραφο έχει αποσταλεί στο email σας. Ευχαριστούμε για την προτίμησή σας!');
        $checkout = $checkoutCompleted->handleSuccessfulPayment($cartItems);

        //Todo: Save checkout into eshopDB in case order failed to save in S1
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
