<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 3/6/2018
 * Time: 2:29 μμ
 */

namespace App\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\{
    ChoiceType, PasswordType, TextType
};
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('username', TextType::class, array(
                'required' => true,
                'label' => 'Όνομα Χρήστη (email)'
            ))
            ->add('password', PasswordType::class)
            ->add('newsletter', ChoiceType::class, array(
                'expanded' => true,
                'multiple' => true
            ))
            ->add('special_offers', ChoiceType::class, array(
                'expanded' => true,
                'multiple' => true
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
//        $resolver->setRequired('username');
    }
}