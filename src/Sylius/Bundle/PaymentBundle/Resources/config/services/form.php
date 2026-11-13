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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\Bundle\PaymentBundle\Form\Type\GatewayConfigType;
use Sylius\Bundle\PaymentBundle\Form\Type\PaymentGatewayChoiceType;
use Sylius\Bundle\PaymentBundle\Form\Type\PaymentMethodChoiceType;
use Sylius\Bundle\PaymentBundle\Form\Type\PaymentMethodTranslationType;
use Sylius\Bundle\PaymentBundle\Form\Type\PaymentMethodType;
use Sylius\Bundle\PaymentBundle\Form\Type\PaymentType;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.form.type.payment.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.payment_method.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.payment_method_translation.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.gateway_config.validation_groups', ['sylius']);

    $services
        ->set('sylius.form.type.gateway_config', GatewayConfigType::class)
        ->args([
            '%sylius.model.gateway_config.class%',
            '%sylius.form.type.gateway_config.validation_groups%',
            service('sylius.form_registry.payment_gateway_config'),
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.payment', PaymentType::class)
        ->args([
            '%sylius.model.payment.class%',
            '%sylius.form.type.payment.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.payment_method', PaymentMethodType::class)
        ->args([
            '%sylius.model.payment_method.class%',
            '%sylius.form.type.payment_method.validation_groups%',
            service('sylius.validator.groups_generator.payment_method'),
            service('sylius.generator.gateway_name'),
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.payment_method_translation', PaymentMethodTranslationType::class)
        ->args([
            '%sylius.model.payment_method_translation.class%',
            '%sylius.form.type.payment_method_translation.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.payment_method_choice', PaymentMethodChoiceType::class)
        ->args([
            service('sylius.resolver.payment_methods'),
            service('sylius.repository.payment_method'),
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.payment_gateway_choice', PaymentGatewayChoiceType::class)
        ->args(['%sylius.payment_gateways%'])
        ->tag('form.type')
    ;
};
