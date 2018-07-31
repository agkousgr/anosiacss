<?php

namespace App\Form\Type;

use App\Entity\Checkout;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{CheckboxType, HiddenType, TextType};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CheckoutStep1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('nextPage', HiddenType::class, array(
                'data' => 2
            ))
            ->add('newsletter', CheckboxType::class, array(
                'required' => false,
            ));
//            ->add('address', TextType::class)
//            ->add('zip', TextType::class)
//            ->add('city', TextType::class)
//            ->add('district', TextType::class)
//            ->add('phone01', TextType::class)
//            ->add('afm', TextType::class)
//            ->add('irs', TextType::class)
//            ->add('special_offers', CheckboxType::class, array(
//                'required' => false,
//            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(["data_class" => Checkout::class]);
        $resolver->setRequired('loggedUser');
    }
}