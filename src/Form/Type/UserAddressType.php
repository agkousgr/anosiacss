<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{
    CheckboxType, PasswordType, TextType, EmailType, RepeatedType
};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('address', TextType::class)
            ->add('zip', TextType::class)
            ->add('city', TextType::class)
            ->add('district', TextType::class)
            ->add('phone01', TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
//        $resolver->setDefault(array(
//            'data_class' => WebserviceUser::class,
//        ));
    }
}