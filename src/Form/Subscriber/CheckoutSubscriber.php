<?php

namespace App\Form\Subscriber;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\{FormEvent, FormEvents};
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CheckoutSubscriber implements EventSubscriberInterface
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var array
     */
    private $options;

    /**
     * CheckoutSubscriber constructor.
     * @param array $options
     * @param Session $session
     */
    public function __construct(array $options, SessionInterface $session)
    {
        $this->session = $session;
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSet',
        ];
    }

    public function onPreSet(FormEvent $event)
    {

        dump($event->getData());
        if (!$this->session->get("anosiaClientId") && $event->getData()->getNextPage() === 2) {

        }

        if (!$this->session->get("anosiaClientId")) {
            $form = $event->getForm();
            $form->add('address', TextType::class)
                ->add('zip', TextType::class)
                ->add('city', TextType::class)
                ->add('district', TextType::class)
                ->add('phone01', TextType::class);
        }
    }
}