<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;

final class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_name' => 'newPassword',
                'first_options' => ['label' => 'sylius.form.user.password.label'],
                'second_name' => 'newPasswordConfirmation',
                'second_options' => ['label' => 'sylius.form.user.password.confirmation'],
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_admin_reset_password';
    }
}
