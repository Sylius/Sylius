<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Form\Type;

use Sylius\Bundle\AdminBundle\Form\Model\PasswordReset;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ResetPasswordType extends AbstractType
{
    public function __construct(private array $validationGroups = [])
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_name' => 'newPassword',
                'first_options' => ['label' => 'sylius.form.admin.reset_password.password.label'],
                'second_name' => 'confirmNewPassword',
                'second_options' => ['label' => 'sylius.form.admin.reset_password.password.confirmation'],
                'invalid_message' => 'sylius.admin.reset_password.mismatch',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PasswordReset::class,
            'validation_groups' => $this->validationGroups,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_admin_reset_password';
    }
}
