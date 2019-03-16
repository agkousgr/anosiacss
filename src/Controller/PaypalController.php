<?php

namespace App\Controller;

use App\Service\PaypalClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class PaypalController extends AbstractController
{
    public function return(Request $request, PaypalClass $paypal)
    {
        $token = $request->query->get('token');
        $PayerID = $request->query->get('PayerID');

        define('PPL_MODE', 'live');

        if (PPL_MODE == 'sandbox') {
            define('PPL_API_USER', 'kefalagr04-facilitator_api1.gmail.com');
            define('PPL_API_PASSWORD', '1370701357');
            define('PPL_API_SIGNATURE', 'A1J62ZRbzLmE9MVtwAlh8fyid10fA0Hj7JHLGM2laTH9dW2lQeWcEd75');
        } else {
            define('PPL_API_USER', 'info_api1.blk.gr');
            define('PPL_API_PASSWORD', 'AJ6YLHGG7AZ99FPJ');
            define('PPL_API_SIGNATURE', 'AFcWxV21C7fd0v3bYYYRCpSSRl31AbaI5JFMgUI-FksBDLxc8vm4Gw-J');
        }

        define('PPL_LOGO_IMG', 'https://www.blk.gr/images/logo.png');
        define('PPL_RETURN_URL', 'https://www.blk.gr/index/paypal/result/checkout/');
        define('PPL_CANCEL_URL', 'https://www.blk.gr/index/paypal/result/cancel');
        define('PPL_CURRENCY_CODE', 'EUR');

        if ($token && $PayerID) {
            //------------------DoExpressCheckoutPayment-------------------
            //Paypal redirects back to this page using ReturnURL, We should receive TOKEN and Payer ID
            //we will be using these two variables to execute the "DoExpressCheckoutPayment"
            //Note: we haven't received any payment yet.

            $paypal->DoExpressCheckoutPayment($token, $PayerID);
            return $this->redirectToRoute('checkout');
        } else {
            throw $this->createNotFoundException('Something went wrong. Checking ...');
        }
    }

    public function cancel()
    {
        return $this->redirectToRoute('checkout');

    }
}