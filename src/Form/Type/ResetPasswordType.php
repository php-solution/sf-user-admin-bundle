<?php

namespace PhpSolution\UserAdminBundle\Form\Type;

use PhpSolution\UserAdminBundle\Entity\UserAdminInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ResetPasswordType
 */
class ResetPasswordType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'plainPassword',
                RepeatedType::class,
                [
                    'first_name' => 'password',
                    'second_name' => 'confirm',
                    'type' => 'password',
                    'invalid_message' => 'The password and confirm password fields must match.',
                    'first_options' => ['label' => 'Password'],
                    'second_options' => ['label' => 'Confirm Password'],
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'validation_groups' => ['edit_password'],
                'data_class' => UserAdminInterface::class,
            ]);
    }
}