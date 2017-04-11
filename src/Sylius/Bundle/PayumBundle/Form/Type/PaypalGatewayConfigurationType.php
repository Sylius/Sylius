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
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class PaypalGatewayConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'sylius.form.gateway_configuration.paypal.username',
                'constraints' => [
                    new NotBlank([
                        'message' => 'sylius.gateway_config.paypal.username.not_blank',
                        'groups' => 'sylius',
                    ])
                ],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'sylius.form.gateway_configuration.paypal.password',
                'constraints' => [
                    new NotBlank([
                        'message' => 'sylius.gateway_config.paypal.password.not_blank',
                        'groups' => 'sylius',
                    ])
                ],
            ])
            ->add('signature', TextType::class, [
                'label' => 'sylius.form.gateway_configuration.paypal.signature',
                'constraints' => [
                    new NotBlank([
                        'message' => 'sylius.gateway_config.paypal.signature.not_blank',
                        'groups' => 'sylius',
                    ])
                ],
            ])
            ->add('sandbox', CheckboxType::class, [
                'label' => 'sylius.form.gateway_configuration.paypal.sandbox'
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $data = $event->getData();

                $data['payum.http_client'] = '@sylius.payum.http_client';
            })
        ;
    }
}
