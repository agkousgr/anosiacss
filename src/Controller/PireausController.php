<?php


namespace App\Controller;


use App\Entity\Checkout;
use App\Entity\PireausResults;
use App\Service\CheckoutService;
use App\Service\PireausRedirection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PireausController extends MainController
{


    public function success(Request $request, CheckoutService $checkoutService)
    {
        $checkout = new Checkout();

        $checkout = $this->session->get('curOrder');
        // Todo: get post or get response and create Hash to validate response
        // Page 26 Pireaus Manual
        dump($request->request, $checkout);



        $TransactionTicket = 'asfasdfa';
//        $TransactionTicket = $checkout->getPireausTranTicket();
        $PosId = 2141384532;
        $AcquirerId = 14;
        $MerchantReference = $checkout->getOrderNo();
        $ApprovalCode = $request->request->get('ApprovalCode');
        $Parameters = $request->request->get('Parameters');
        $ResponseCode = $request->request->get('ResponseCode');
        $SupportReferenceID = $request->request->get('SupportReferenceID');
        $AuthStatus = $request->request->get('AuthStatus');
        $PackageNo = $request->request->get('PackageNo');
        $StatusFlag = $request->request->get('StatusFlag');

        $myHash = pack('H', hash('sha256', $TransactionTicket . $PosId . $AcquirerId . $MerchantReference . $ApprovalCode . $Parameters . $ResponseCode . $SupportReferenceID . $AuthStatus . $PackageNo . $StatusFlag));

        dump($myHash);


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

    public function failure(Request $request, EntityManagerInterface $em)
    {
        $pireaus = new PireausResults();
        $pireaus->setCliendId($request->request->get('clientId'));
        $pireaus->setMerchantReference();
//        dump($request);
     die();
        $this->addFlash(
            'notice',
            'Η συναλλαγή σας δεν ολοκληρώθηκε "Invalid Card number/Exp Month/Exp Year". Παρακαλούμε ελέγξτε τα στοιχεία της κάρτας σας και ξαναπροσπαθήστε.'
        );
        return $this->redirectToRoute('checkout');
    }

    public function pireausIframe(Request $request)
    {
        try {
            return $this->render('orders/pireaus_iframe.html.twig');
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}