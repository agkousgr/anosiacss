<?php

namespace App\Service\Mail;

use App\Entity\Checkout;

/**
 * Class OrderMailer
 */
class OrderMailer extends Mailer
{
    //TODO: Sender must be an app parameter
    //TODO: Get required variables from either local database or external API

    public function buildAndSendCourier(Checkout $order)
    {
        $params = [
            'subject' => 'Anosia Pharmacy - Επιτυχής λήψη της παραγγελίας σας',
            'sender' => 'info@anosiapharmacy.gr',
            'recipient' => $order->getEmail(),
        ];
        $message = $this->templateEngine->render('email_templates/order_courier.html.twig', [
            'order' => '',
            'proposedProducts' => [],
        ]);

        $this->send($message, $params);
    }

    public function buildAndSendTakeAway(Checkout $order)
    {
        $params = [
            'subject' => 'Anosia Pharmacy - Επιτυχής λήψη της παραγγελίας σας',
            'sender' => 'info@anosiapharmacy.gr',
            'recipient' => $order->getEmail(),
        ];
        $message = $this->templateEngine->render('email_templates/order_take_away.html.twig', [
            'order' => '',
            'proposedProducts' => [],
        ]);

        $this->send($message, $params);
    }

    public function buildAndSendVoucher(Checkout $order)
    {
        $params = [
            'subject' => 'Anosia Pharmacy - Αποστολή της παραγγελίας σας',
            'sender' => 'info@anosiapharmacy.gr',
            'recipient' => $order->getEmail(),
        ];
        $message = $this->templateEngine->render('email_templates/order_voucher.html.twig', [
            'voucher' => '',
            'offers' => [],
        ]);

        $this->send($message, $params);
    }

    public function buildAndSendAfterSales(Checkout $order)
    {
        $params = [
            'subject' => 'Anosia Pharmacy - Ενημέρωση για την παραγγελίας σας',
            'sender' => 'info@anosiapharmacy.gr',
            'recipient' => $order->getEmail(),
        ];
        $message = $this->templateEngine->render('email_templates/order_after_sales.html.twig', [
            'offers' => [],
        ]);

        $this->send($message, $params);
    }

    /**
     * {@inheritDoc}
     */
    protected function send(string $message, array $params)
    {
        try {
            $toBeSent = (new \Swift_Message())
                ->setSubject($params['subject'])
                ->setFrom($params['sender'])
                ->setTo($params['recipient'])
                ->setBody($message, 'text/html');
            $this->mailer->send($toBeSent);
        } catch (\Exception $e) {
            //TODO: Handle failure properly
        }
    }
}
