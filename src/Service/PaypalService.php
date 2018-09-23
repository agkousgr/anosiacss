<?php

namespace App\Service;

use App\Service\PaypalClass;

class PaypalService
{
    /**
     * @var PaypalClass
     */
    private $paypal;

    public function __construct(PaypalClass $paypal)
    {
        $this->paypal = $paypal;
    }

    public function sendToPaypal($checkout)
    {
        define('PPL_MODE', 'sandbox');

        if (PPL_MODE == 'sandbox') {
            define('PPL_API_USER', 'john-facilitator_api1.3works.gr');
            define('PPL_API_PASSWORD', 'F78U5M9P2RZD2QTQ');
            define('PPL_API_SIGNATURE', 'A-X91d6dvj07IIDTUn5hM8p8w8LxAVYxiOpzxcPuC9RLCYmPR3rQ5U1q');
        } else {
//            define('PPL_API_USER', 'info_api1.blk.gr');
//            define('PPL_API_PASSWORD', 'AJ6YLHGG7AZ99FPJ');
//            define('PPL_API_SIGNATURE', 'AFcWxV21C7fd0v3bYYYRCpSSRl31AbaI5JFMgUI-FksBDLxc8vm4Gw-J');
        }

//        $lang =
        define('PPL_LANG', 'el');

        define('PPL_LOGO_IMG', 'https://i0.wp.com/www.anosiapharmacy.gr/wp-content/uploads/2017/03/logo.png');

        define('PPL_RETURN_URL', 'http://anosia.democloudon.cloud/public//paypal/result');
        define('PPL_CANCEL_URL', 'http://anosia.democloudon.cloud/public//paypal/cancel');

        define('PPL_CURRENCY_CODE', 'EUR');

//        $now = $date = new \DateTime("now");
//        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
//            $ip = $_SERVER['HTTP_CLIENT_IP'];
//        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
//            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
//        } else {
//            $ip = $_SERVER['REMOTE_ADDR'];
//        }

        if (!isset($_GET['token'])) {


            $orderid = $checkout->getOrderNo();
            $orderDesc = $checkout->getComments();
            $orderAmount = $checkout->getTotalOrderCost();

            $products = array();

            // set an item via POST request
            $products[0]['ItemName'] = $orderid;
            $products[0]['ItemPrice'] = $orderAmount; //Item Price
            $products[0]['ItemNumber'] = '1'; //Item Number
            $products[0]['ItemDesc'] = $orderDesc; //Item Number
            $products[0]['ItemQty'] = 1; // Item Quantity
            //-------------------- prepare charges -------------------------

            $charges = array();

            //Other important variables like tax, shipping cost
            $charges['TotalTaxAmount'] = 0;  //Sum of tax for all items in this order.
            $charges['HandalingCost'] = 0;  //Handling cost for this order.
            $charges['InsuranceCost'] = 0;  //shipping insurance cost for this order.
            $charges['ShippinDiscount'] = 0; //Shipping discount for this order. Specify this as negative number.
            $charges['ShippinCost'] = 0; //Although you may change the value later, try to pass in a shipping amount that is reasonably accurate.
            //------------------SetExpressCheckOut-------------------
            //We need to execute the "SetExpressCheckOut" method to obtain paypal token

            $this->paypal->SetExpressCheckOut($products, $charges);
        } elseif ($_GET['token'] != '' && $_GET['PayerID'] != '') {

            //------------------DoExpressCheckoutPayment-------------------
            //Paypal redirects back to this page using ReturnURL, We should receive TOKEN and Payer ID
            //we will be using these two variables to execute the "DoExpressCheckoutPayment"
            //Note: we haven't received any payment yet.

            $this->paypal->DoExpressCheckoutPayment($checkout->getOrderNo());
        }
    }
}