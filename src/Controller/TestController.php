<?php


namespace App\Controller;



use Symfony\Component\HttpFoundation\Response;

class TestController
{
    public function testEmail(\Swift_Mailer $mailer)
    {
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('send@example.com')
            ->setTo('john@3works.gr')
            ->setBody('You should see me from the profiler!');

        $mailer->send($message);
        return new Response('ok');
    }
}