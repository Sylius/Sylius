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

namespace Sylius\Bundle\PayumBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class PaypalGatewayConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'sylius.form.gateway_configuration.paypal.username',
            ])
            ->add('password', TextType::class, [
                'label' => 'sylius.form.gateway_configuration.paypal.password',
            ])
            ->add('signature', TextType::class, [
                'label' => 'sylius.form.gateway_configuration.paypal.signature',
            ])
            ->add('sandbox', CheckboxType::class, [
                'label' => 'sylius.form.gateway_configuration.paypal.sandbox',
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $data = $event->getData();

                $data['payum.http_client'] = '@sylius.payum.http_client';
            })
        ;
    }
}
