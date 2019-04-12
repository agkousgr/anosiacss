<?php

namespace App\Form\Type;

use App\Entity\Checkout;
use App\Form\Subscriber\CheckoutSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{ChoiceType,
    CheckboxType,
    HiddenType,
    TextareaType,
    TextType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CheckoutStep2Type extends AbstractType
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
            ->add('paymentType', ChoiceType::class, array(
                'choices' => Checkout::PAYMENT_TYPES,
                'multiple'=>false,
                'expanded'=>true
            ))
            ->add('comments', TextareaType::class, array(
                'required' => false,
                'attr' => [
                    'placeholder' => 'Γενικές παρατηρήσεις'
                ]
            ))
            ->add('agreeTerms', CheckboxType::class, array(
                'label' => 'Έχω διαβάσει και αποδέχομαι ανεπιφύλακτα τους Όρους Χρήσης'
            ))
        ;

//        $builder->addEventSubscriber(new CheckoutSubscriber($options, $this->session));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
//        $resolver->setDefault(array(
//            'data_class' => WebUser::class,
//        ));
    }
}