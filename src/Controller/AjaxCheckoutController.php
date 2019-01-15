<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Checkout;
use App\Service\CheckoutService;
use App\Service\UserAccountService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AjaxCheckoutController extends AbstractController
{
    public function getAddress(Request $request, CheckoutService $checkoutService, LoggerInterface $logger, SessionInterface $session)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                $id = $request->request->getInt('id');
                if (!$id) {
                    throw $this->createNotFoundException(
                        'No product found for id ' . $id
                    );
                }
                $address = new Address();
                $checkoutService->getAddress($session->get("anosiaClientId"), $address, $id);
                return $this->json(['success' => true, 'address' => $address]);
            } catch (\Exception $e) {
                $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
                throw $e;
            }
        } else {
            throw $this->createNotFoundException('The resource you are looking for could not be found.');
        }
    }

    public function step1Submit(Request $request, CheckoutService $checkoutService, UserAccountService $userAccountService, LoggerInterface $logger, SessionInterface $session)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                // Todo: check for existing user on username lost focus
//                if (null === $this->loggedUser) {
//                    $userExist = $userAccountService->checkIfUserExist($checkout->getEmail());
//                    if ($userExist) {
//                        $curStep = 1;
//                        $this->addFlash(
//                            'notice',
//                            'Υπάρχει ήδη χρήστης με το email που εισάγατε. Αν έχετε ήδη λογαριασμό κάντε login!'
//                        );
//                    }
//                }
                $checkout = $session->get('curOrder');
                if ($session->get("addAddress")) {
                    $userAccountService->updateUserInfo($checkout);
                    $session->remove('addAddress');
                }
                $checkout->setFirstname($request->request->get('checkout_step1')['firstname']);
                $checkout->setLastname($request->request->get('checkout_step1')['lastname']);
                $checkout->setEmail($request->request->get('checkout_step1')['email']);
                $checkout->setAfm($request->request->get('checkout_step1')['afm']);
                $checkout->setIrs($request->request->get('checkout_step1')['irs']);
                if ($request->request->get('checkout_step1')['shipAddress']) {
                    $checkout->setShipAddress($request->request->get('checkout_step1')['shipAddress']);
                    $checkout->setShipZip($request->request->get('checkout_step1')['shipZip']);
                    $checkout->setShipCity($request->request->get('checkout_step1')['shipCity']);
                    $checkout->setShipDistrict($request->request->get('checkout_step1')['shipDistrict']);
                }
                $checkout->setShippingType($request->request->get('checkout_step1')['shippingType']);
                if ($request->request->get('checkout_step1')['shippingType'] === '1000') {
                    $checkout->setShippingCost(2.00);
                }
                $checkout->setComments($request->request->get('checkout_step1')['comments']);
                $session->set('curOrder', $checkout);
//                $id = $request->request->getInt('id');
                dump($request, $session->get('curOrder'), $request->request->get('checkout_step1'));
                return $this->json(['success' => true]);
            } catch (\Exception $e) {
                $logger->error(__METHOD__ . ' -> {message}', ['message' => $e->getMessage()]);
                throw $e;
            }
        } else {
            throw $this->createNotFoundException('The resource you are looking for could not be found.');
        }
    }
}