<?php


namespace App\Controller;


use App\Entity\Checkout;
use App\Service\PireausRedirection;

class PireausController
{
    public function sentToPireaus(PireausRedirection $pireausRedirection)
    {
        $checkout = new Checkout();
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 10; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
//        return $randomString;
        $checkout->setOrderNo($randomString);
        $checkout->setTotalOrderCost(1);
        $checkout->setInstallments(0);
        $pireausRedirection->submitOrderToPireaus($checkout);
    }
}