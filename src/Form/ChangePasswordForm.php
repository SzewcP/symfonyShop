<?php
namespace App\Form;

use App\Entity\ChangePassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, array(
                'attr' => array('class' => 'form-control'),
                'error_bubbling' => true,
            ))
            ->add('newPassword', RepeatedType::class, array(
                'invalid_message' => 'The password fields must match.',
                'error_bubbling' => true,
                'type' => PasswordType::class,
                'first_options' => array('label' => 'Password',
                    'attr' => array('class' => 'form-control')),
                'second_options' => array('label' => 'Repeat Password',
                    'attr' => array('class' => 'form-control'))
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ChangePassword::class,
        ]);
    }

}