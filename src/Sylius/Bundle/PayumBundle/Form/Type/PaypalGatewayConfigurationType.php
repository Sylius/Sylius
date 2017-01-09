<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PayumBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class PaypalGatewayConfigurationType extends AbstractType implements GatewayConfigurationTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'sylius.form.payment_method.gateway_configuration.paypal.username',
            ])
            ->add('password', PasswordType::class, [
                'label' => 'sylius.form.payment_method.gateway_configuration.paypal.password',
            ])
            ->add('signature', TextType::class, [
                'label' => 'sylius.form.payment_method.gateway_configuration.paypal.signature',
            ])
        ;
    }
}
