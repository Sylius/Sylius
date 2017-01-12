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
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
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
            ->add('payum_http_client', HiddenType::class, [
                'label' => false,
                // Hardcoded, as its the only payum client used now in sylius, could be extended to support many clients
                'data' => '@sylius.payum.http_client',
            ])
            ->addModelTransformer(new CallbackTransformer(
                function ($modelData) {
                    if (empty($modelData)) {
                        return $modelData;
                    }

                    $modelData['payum_http_client'] = $modelData['payum.http_client'];
                    unset($modelData['payum.http_client']);

                    return $modelData;
                },
                function ($formData) {
                    $formData['payum.http_client'] = $formData['payum_http_client'];
                    unset($formData['payum_http_client']);

                    return $formData;
                }
            ))
        ;
    }
}
