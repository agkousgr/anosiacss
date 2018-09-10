<?php

namespace App\Form\Type;

use App\Form\Subscriber\CheckoutSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{ChoiceType, HiddenType, TextType};
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
            ->add('nextPage', HiddenType::class, array(
                'data' => 3
            ))
            ->add('series', ChoiceType::class, array(
                'choices' => array(
                    'Απόδειξη' => 7021,
                    'Τιμολόγιο' => 7023,
                ),
                'multiple'=>false,
                'expanded'=>true
            ))
            ->add('afm', TextType::class, array(
                'data' => 'No Invoice'
            ))
            ->add('irs', TextType::class, array(
                'data' => 'No Invoice'
            ))
            ->add('shipAddress', TextType::class, [
                'required' => false
            ])
            ->add('shipZip', TextType::class, [
                'required' => false
            ])
            ->add('shipCity', TextType::class, [
                'required' => false
            ])
            ->add('shipDistrict', TextType::class, [
                'required' => false
            ])
        ;

        $builder->addEventSubscriber(new CheckoutSubscriber($options, $this->session));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
//        $resolver->setDefault(array(
//            'data_class' => WebUser::class,
//        ));
    }
}