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

    public function checkIfUserExists(Request $request, SessionInterface $session, UserAccountService $userAccountService)
    {
        if (null === $session->get("anosiaUser")) {
            $userExist = $userAccountService->checkIfUserExist($request->request->get('email'));
            if ($userExist) {
                return $this->json(['success' => false, 'errorMsg' => 'Υπάρχει ήδη χρήστης με το email που εισάγατε. Αν έχετε ήδη λογαριασμό κάντε login!']);
            }
            return $this->json(['success' => true]);
        }
    }

    public function step1Submit(Request $request, UserAccountService $userAccountService, LoggerInterface $logger, SessionInterface $session)
    {
        if ($request->isXmlHttpRequest()) {
            try {
                /** @var \App\Entity\Checkout */
                $checkout = $session->get('curOrder');
                $checkout->setFirstname($request->request->get('checkout_step1')['firstname']);
                $checkout->setLastname($request->request->get('checkout_step1')['lastname']);
                $checkout->setEmail($request->request->get('checkout_step1')['email']);
                $checkout->setAfm($request->request->get('checkout_step1')['afm']);
                $checkout->setIrs($request->request->get('checkout_step1')['irs']);
                if ($request->request->get('checkout_step1')['shipAddress']) {
                    $checkout->setRecepientName($request->request->get('checkout_step1')['recepientName']);
                    $checkout->setShipAddress($request->request->get('checkout_step1')['shipAddress']);
                    $checkout->setShipZip($request->request->get('checkout_step1')['shipZip']);
                    $checkout->setShipCity($request->request->get('checkout_step1')['shipCity']);
                    $checkout->setShipDistrict($request->request->get('checkout_step1')['shipDistrict']);
                }
                if (array_key_exists('newsletter', $request->request->get('checkout_step1'))) {
                    $checkout->setNewsletter(true);
                    $checkout->setNewsLetterAge($request->request->get('checkout_step1')['newsLetterAge']);
                    $checkout->setNewsLetterGender($request->request->get('checkout_step1')['newsLetterGender']);
                }
                $checkout->setVoucherComments($request->request->get('checkout_step1')['voucherComments']);
                $checkout->setShippingType($request->request->get('checkout_step1')['shippingType']);
                $checkout->setAddress($request->request->get('checkout_step1')['address']);
                $checkout->setZip($request->request->get('checkout_step1')['zip']);
                $checkout->setCity($request->request->get('checkout_step1')['city']);
                $checkout->setDistrict($request->request->get('checkout_step1')['district']);
                $checkout->setPhone01($request->request->get('checkout_step1')['phone01']);
                if (null !== $session->get("anosiaClientId"))
                    $checkout->setClientId($session->get("anosiaClientId"));
                else {
                    $clientId = $userAccountService->updateUserInfo($checkout);
                    $checkout->setClientId($clientId);

                }
                $session->remove('addAddress');
                if ($request->request->get('checkout_step1')['shippingType'] !== '104')
                    $checkout->setShippingCost(0);
                $session->set('curOrder', $checkout);

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