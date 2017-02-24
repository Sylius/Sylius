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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class StripeGatewayConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('secret_key', TextType::class, [
                'label' => 'sylius.form.gateway_configuration.stripe.secret_key',
                'constraints' => [
                    new NotBlank([
                        'message' => 'sylius.gateway_config.stripe.secret_key.not_blank',
                        'groups' => 'sylius',
                    ])
                ],
            ])
            ->add('publishable_key', TextType::class, [
                'label' => 'sylius.form.gateway_configuration.stripe.publishable_key',
                'constraints' => [
                    new NotBlank([
                        'message' => 'sylius.gateway_config.stripe.publishable_key.not_blank',
                        'groups' => 'sylius',
                    ])
                ],
            ])
        ;
    }
}
