<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;


class ForgotPasswordType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('email', EmailType::class, array(
                'label' => false,
                'required' => true,
                'attr' => array(
                    'class' => 'form-control',
                    'placeholder' => 'E-mail'
                )
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Wyślij nowe hasło',
                'attr' => array(
                    'class' => 'btn btn-lg btn-success btn-block',
                    'style' => 'margin-top:15px;margin-bottom:10px;'
                )
            ))
        ;
    }
}