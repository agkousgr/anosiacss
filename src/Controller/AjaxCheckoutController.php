<?php

namespace App\Controller;

use App\Entity\Address;
use App\Service\CheckoutService;
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
}