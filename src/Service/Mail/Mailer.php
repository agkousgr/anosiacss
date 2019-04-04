<?php

namespace App\Service\Mail;

use Symfony\Component\Templating\EngineInterface;
use \Swift_Mailer;

/**
 * Class Mailer
 */
abstract class Mailer
{
    /**
     * @var Swift_Mailer
     */
    protected $mailer;

    /**
     * @var EngineInterface
     */
    protected $templateEngine;

    /**
     * Constructor.
     *
     * @param Swift_Mailer    $mailer
     * @param EngineInterface $engine
     */
    public function __construct(Swift_Mailer $mailer, EngineInterface $engine)
    {
        $this->mailer = $mailer;
        $this->templateEngine = $engine;
    }

    /**
     * Email message based on params.
     *
     * @param string $message
     * @param array  $params
     *
     * @return mixed
     */
    abstract protected function send(string $message, array $params);
}
