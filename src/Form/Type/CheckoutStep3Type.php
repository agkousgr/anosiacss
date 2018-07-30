<?php

namespace App\Form\Type;

use App\Entity\Checkout;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{ChoiceType, HiddenType, TextareaType, TextType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CheckoutStep3Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nextPage', HiddenType::class, array(
                'data' => 4
            ))
            ->add('shippingType', ChoiceType::class, array(
                'choices' => Checkout::SHIPPING_TYPES,
                'multiple'=>false,
                'expanded'=>true
            ))
            ->add('comments', TextareaType::class, array(
                'required' => false
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
//        $resolver->setDefault(array(
//            'data_class' => WebUser::class,
//        ));
    }
}