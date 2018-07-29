<?php

namespace App\Form\Type;

use App\Entity\Checkout;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{CheckboxType, ChoiceType, HiddenType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CheckoutStep4Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('paymentType', ChoiceType::class, array(
                'choices' => Checkout::PAYMENT_TYPES,
                'multiple'=>false,
                'expanded'=>true
            ))
            ->add('agreeTerms', CheckboxType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
//        $resolver->setDefault(array(
//            'data_class' => WebUser::class,
//        ));
    }
}