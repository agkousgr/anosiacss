<?php

namespace App\Form\Type;

use App\Entity\Checkout;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{CheckboxType, EmailType, HiddenType, TextType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CheckoutStep1Type extends AbstractType
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * CheckoutStep2Type constructor.
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('email', EmailType::class)
            ->add('nextPage', HiddenType::class, array(
                'data' => 2
            ))
            ->add('newsletter', CheckboxType::class, array(
                'required' => false,
            ));

        $builder->addEventSubscriber(new CheckoutSubscriber($options, $this->session));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(["data_class" => Checkout::class]);
        $resolver->setRequired('loggedUser');
    }
}