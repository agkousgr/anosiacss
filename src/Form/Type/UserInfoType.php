<?php

namespace App\Form\Type;

use App\Security\User\WebserviceUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{
    CheckboxType, PasswordType, TextType, EmailType, RepeatedType
};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('username', EmailType::class, array(
                'required' => true,
                'label' => 'Όνομα Χρήστη (email)'
            ))
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType::class
            ))
            ->add('newsletter', CheckboxType::class, array(
                'required' => false,
            ));
//            ->add('special_offers', CheckboxType::class, array(
//                'required' => false,
//            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
//        $resolver->setDefault(array(
//            'data_class' => WebserviceUser::class,
//        ));
    }
}