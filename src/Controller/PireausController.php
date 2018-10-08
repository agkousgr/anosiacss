<?php


namespace App\Controller;


use App\Entity\Checkout;
use App\Service\CheckoutService;
use App\Service\PireausRedirection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PireausController extends MainController
{
    public function success(Request $request, SessionInterface $session, CheckoutService $checkoutService)
    {
        // Todo: get post or get response and create Hash to validate response
        // Page 26 Pireaus Manual
        dump($request);
        die();
        $checkout = $session->get('curOrder');
        $orderResponse = $checkoutService->submitOrder($checkout, $this->cartItems);
        if ($orderResponse) {

            $this->addFlash(
                'success',
                'Η παραγγελία σας ολοκληρώθηκε με επιτυχία. Ένα αντίγραφο έχει αποσταλεί στο email σας ' . $checkout->getEmail() . '. Ευχαριστούμε που μας προτιμήσατε για τις αγορές σας!'
            );
            $checkoutService->sendOrderConfirmationEmail($checkout);
//                    $checkoutService->emptyCart($this->cartItems, $em);
            $orderCompleted = true;
        } else {
            $this->addFlash(
                'notice',
                'Ένα σφάλμα παρουσιάστηκε κατά την διαδικασία της παραγγελίας σας. Η online πληρωμή έχει πραγματοποιηθεί. Παρακαλούμε επικοινωνήστε μαζί μας! Ο κωδικός της παραγγελίας σας είναι ' . $checkout->getOrderNo()
            );

        }
        // Todo: CLEAR curOrder SESSION
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
    }

    public function cancel()
    {
        $this->addFlash(
            'notice',
            'Η online πληρωμή ακυρώθηκε.'
        );

        return $this->redirectToRoute('checkout');
    }
}