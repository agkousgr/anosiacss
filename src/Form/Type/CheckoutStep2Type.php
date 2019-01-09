<?php

namespace App\Form\Type;

use App\Entity\Checkout;
use App\Form\Subscriber\CheckoutSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{ChoiceType, CheckboxType, HiddenType, TextType};
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
//            ->add('nextPage', HiddenType::class, array(
//                'data' => 3
//            ))
//            ->add('series', ChoiceType::class, array(
//                'choices' => array(
//                    'Απόδειξη' => 7021,
//                    'Τιμολόγιο' => 7023,
//                ),
//                'multiple'=>false,
//                'expanded'=>true
//            ))
//            ->add('afm', TextType::class, array(
//                'data' => 'No Invoice'
//            ))
//            ->add('irs', TextType::class, array(
//                'data' => 'No Invoice'
//            ))
//            ->add('shipAddress', TextType::class, [
//                'required' => false
//            ])
//            ->add('shipZip', TextType::class, [
//                'required' => false
//            ])
//            ->add('shipCity', TextType::class, [
//                'required' => false
//            ])
//            ->add('shipDistrict', TextType::class, [
//                'required' => false
//            ])
            ->add('paymentType', ChoiceType::class, array(
                'choices' => Checkout::PAYMENT_TYPES,
                'multiple'=>false,
                'expanded'=>true
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