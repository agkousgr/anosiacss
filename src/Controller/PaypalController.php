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
        $checkout = $checkoutCompleted->handleSuccessfulPayment($cartItems);

        if ($checkout->isValid()) {
            $this->addFlash('success', 'Η συναλλαγή ολοκληρώθηκε με επιτυχία! Ένα αντίγραφο έχει αποσταλεί στο email σας. Ευχαριστούμε για την προτίμησή σας!');
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
        }else{
            $this->addFlash('notice', 'Η παραγγελία σας δεν καταχωρήθηκε. Παρακαλώ δοκιμάστε ξανά ή επικοινωνήστε με το κατάστημά μας χρησιμοποιώντας τον κωδικό παραγγελίας.');
            return $this->redirectToRoute('checkout');
        }
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
