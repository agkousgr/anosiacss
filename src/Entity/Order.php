<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 22/7/2018
 * Time: 2:53 μμ
 */

namespace App\Entity;


class Order extends WebUser
{
    const SHIPPMENT_TYPES = [
        '1000' => 'Πληρωμή στο κατάστημα',
        '1001' => 'Πιστωτική ',
        '1002' => 'Paypal ',
        '1003' => 'Αντικαταβολή',
        '1004' => 'Κατάθεση σε τράπεζα',
    ];

    const PAYMENT_TYPES = [
        '1000' => 'Courier',
        '1001' => 'Παραλαβή από το κατάστημα',
    ];

    /**
     * @var array
     */
    private $shipmentTypes;

    /**
     * @var array
     */
    private $paymentTypes;

    /**
     * @return array
     */
    public function getShipmentTypes(): array
    {
        return $this->shipmentTypes;
    }

    /**
     * @param array $shipmentTypes
     */
    public function setShipmentTypes(array $shipmentTypes): void
    {
        $this->shipmentTypes = $shipmentTypes;
    }

    /**
     * @return array
     */
    public function getPaymentTypes(): array
    {
        return $this->paymentTypes;
    }

    /**
     * @param array $paymentTypes
     */
    public function setPaymentTypes(array $paymentTypes): void
    {
        $this->paymentTypes = $paymentTypes;
    }
}