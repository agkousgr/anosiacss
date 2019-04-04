<?php


namespace App\Controller;


use App\Entity\Checkout;
use App\Entity\PireausTransaction;
use App\Service\CheckoutCompleted;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PireausController extends MainController
{
    public function success(Request $request, EntityManagerInterface $em, CheckoutCompleted $checkoutCompleted, SessionInterface $session)
    {
        try {
            /** @var Checkout $checkout */
            $checkout = $this->session->get('curOrder');// Todo: get post or get response and create Hash to validate response
            // Page 26 Pireaus Manual
            $TransactionTicket = $checkout->getPireausTranTicket();
            $PosId = 2141384532;
            $AcquirerId = 14;
            $MerchantReference = $checkout->getOrderNo();
            $ApprovalCode = $request->request->get('ApprovalCode');
            $Parameters = $request->request->get('Parameters');
            $ResponseCode = $request->request->get('ResponseCode');
            $SupportReferenceID = $request->request->get('SupportReferenceID');
            $AuthStatus = $request->request->get('AuthStatus');
            $PackageNo = intval($request->request->get('PackageNo'));
            $StatusFlag = $request->request->get('StatusFlag');
            $PireausHash = $request->request->get('HashKey');

            $myHash = strtoupper(hash_hmac('sha256', $TransactionTicket . ';' . $PosId . ';' . $AcquirerId . ';' . $MerchantReference . ';' . $ApprovalCode . ';' . $Parameters . ';' . $ResponseCode . ';' . $SupportReferenceID . ';' . $AuthStatus . ';' . $PackageNo . ';' . $StatusFlag, $TransactionTicket));

            $pireaus = new PireausTransaction();
            $pireaus->setClientId($session->get('anosiaClientId'))
                ->setMerchantReference($MerchantReference)
                ->setStatusFlag($StatusFlag)
                ->setResultCode($request->request->get('ResultCode'))
                ->setSupportReferenceId($SupportReferenceID)
                ->setApprovalCode($ApprovalCode)
                ->setResponseCode($ResponseCode)
                ->setPackageNo($PackageNo)
                ->setAuthStatus($AuthStatus)
                ->setCreatedAt(new \DateTime('now', new \DateTimeZone('Europe/Athens')))
                ->setResponseDescription($request->request->get('ResponseDescription'))
                ->setResultDescription($request->request->get('ResultDescription'));
            $em->persist($pireaus);
            $em->flush();


            if ($myHash === $PireausHash) {
                $cartItems = $this->cartItems;
            } else {
//                $checkoutCompleted->handleFailedPayment();
                $this->addFlash('notice', 'H πληρωμή σας μέσω πιστωτικής απέτυχε. Παρακαλώ δοκιμάστε ξανά ή αλλάξτε τρόπο πληρωμής');
                return $this->redirectToRoute('checkout');
            }

            $this->addFlash('success', 'Η συναλλαγή ολοκληρώθηκε με επιτυχία! Ένα αντίγραφο έχει αποσταλεί στο email σας. Ευχαριστούμε για την προτίμησή σας!');
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
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function cancel()
    {
        $this->addFlash(
            'notice',
            'Η online πληρωμή ακυρώθηκε.'
        );

        return $this->redirectToRoute('checkout');
    }

    public function failure(Request $request, SessionInterface $session, EntityManagerInterface $em)
    {

        try {
            $pireaus = new PireausTransaction();
            $pireaus->setClientId($session->get('anosiaClientId'))
                ->setMerchantReference($session->get('curOrder')->getOrderNo())
                ->setStatusFlag($request->request->get('StatusFlag'))
                ->setResultCode($request->request->get('ResultCode'))
                ->setSupportReferenceId($request->request->get('SupportReferenceID'))
                ->setApprovalCode($request->request->get('ApprovalCode'))
                ->setResponseCode($request->request->get('ResponseCode'))
                ->setPackageNo(intval($request->request->get('PackageNo')))
                ->setAuthStatus($request->request->get('AuthStatus'))
                ->setCreatedAt(new \DateTime('now', new \DateTimeZone('Europe/Athens')))
                ->setResponseDescription($request->request->get('ResponseDescription'))
                ->setResultDescription($request->request->get('ResultDescription'));
            $em->persist($pireaus);
            $em->flush();

            $this->addFlash(
                'notice',
                'Η συναλλαγή σας δεν ολοκληρώθηκε "Invalid Card number/Exp Month/Exp Year". Παρακαλούμε ελέγξτε τα στοιχεία της κάρτας σας και ξαναπροσπαθήστε.'
            );
            return $this->redirectToRoute('checkout');
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function pireausIframe(Request $request)
    {
        try {
            return $this->render('orders/pireaus_iframe.html.twig', [
                'categories' => $this->categories,
                'topSellers' => $this->topSellers,
                'popular' => $this->popular,
                'featured' => $this->featured,
                'checkout' => $this->session->get('curOrder'),
                'loggedUser' => $this->loggedUser,
                'totalCartItems' => $this->totalCartItems,
                'totalWishlistItems' => $this->totalWishlistItems,
                'cartItems' => $this->cartItems,
                'loginUrl' => $this->loginUrl
            ]);
        } catch (\Exception $e) {
            $this->logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}