<?php

namespace App\Form\Type;

use App\Entity\Checkout;
use App\Form\Subscriber\CheckoutSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{CheckboxType, EmailType, HiddenType, TextType, ChoiceType, TextareaType};
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
            ->add('address', TextType::class)
            ->add('zip', TextType::class)
            ->add('city', TextType::class)
            ->add('district', TextType::class)
            ->add('phone01', TextType::class)
            ->add('newsletter', CheckboxType::class, array(
                'required' => false,
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
            ->add('shippingType', ChoiceType::class, array(
                'choices' => Checkout::SHIPPING_TYPES,
                'multiple'=>false,
                'expanded'=>true
            ))
            ->add('comments', TextareaType::class, array(
                'required' => false
            ))
        ;

//        $builder->addEventSubscriber(new CheckoutSubscriber($options, $this->session));
//        $builder->addEventSubscriber(new CheckoutSubscriber($options, $this->session));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(["data_class" => Checkout::class]);
        $resolver->setRequired('loggedUser');
    }
}