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

use Sylius\Bundle\PaymentBundle\Generator\GatewayNameGenerator;
use Sylius\Bundle\PaymentBundle\Generator\GatewayNameGeneratorInterface;
use Sylius\Bundle\PaymentBundle\Validator\GroupsGenerator\GatewayConfigGroupsGenerator;
use Sylius\Bundle\PaymentBundle\Validator\GroupsGenerator\GatewayConfigGroupsGeneratorInterface;
use Sylius\Bundle\PaymentBundle\Validator\GroupsGenerator\PaymentMethodGroupsGenerator;
use Sylius\Bundle\PaymentBundle\Validator\GroupsGenerator\PaymentMethodGroupsGeneratorInterface;
use Sylius\Bundle\ResourceBundle\Form\Registry\FormTypeRegistry;
use Sylius\Component\Payment\Factory\PaymentFactory;
use Sylius\Component\Payment\Factory\PaymentFactoryInterface;
use Sylius\Component\Payment\Resolver\CompositeMethodsResolver;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolver;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;
use Sylius\Component\Registry\PrioritizedServiceRegistry;

return static function (ContainerConfigurator $container) {
    $container->import('services/*.php');

    $parameters = $container->parameters();
    $services = $container->services();

    $parameters->set('sylius.payment_methods_resolver.interface', PaymentMethodsResolverInterface::class);

    $services
        ->set('sylius.custom_factory.payment', PaymentFactory::class)
        ->decorate('sylius.factory.payment', null, 256)
        ->args([service('sylius.custom_factory.payment.inner')])
    ;

    $services->alias(PaymentFactoryInterface::class, 'sylius.factory.payment');

    $services
        ->set('sylius.registry.payment_methods_resolver', PrioritizedServiceRegistry::class)
        ->args([
            '%sylius.payment_methods_resolver.interface%',
            'Payment methods resolver',
        ])
    ;

    $services
        ->set('sylius.resolver.payment_methods', CompositeMethodsResolver::class)
        ->args([service('sylius.registry.payment_methods_resolver')])
    ;
    $services->alias('sylius.resolver.payment_methods.composite', 'sylius.resolver.payment_methods');

    $services->alias(PaymentMethodsResolverInterface::class, 'sylius.resolver.payment_methods');

    $services
        ->set('sylius.resolver.payment_methods.default', PaymentMethodsResolver::class)
        ->args([service('sylius.repository.payment_method')])
        ->tag('sylius.payment_method_resolver', ['type' => 'default', 'label' => 'Default'])
    ;

    $services->set('sylius.form_registry.payment_gateway_config', FormTypeRegistry::class);

    $services
        ->set('sylius.validator.groups_generator.gateway_config', GatewayConfigGroupsGenerator::class)
        ->args([
            '%sylius.form.type.gateway_config.validation_groups%',
            '%sylius.gateway_config.validation_groups%',
        ])
    ;
    $services->alias(GatewayConfigGroupsGeneratorInterface::class, 'sylius.validator.groups_generator.gateway_config');

    $services
        ->set('sylius.validator.groups_generator.payment_method', PaymentMethodGroupsGenerator::class)
        ->args([
            '%sylius.form.type.payment_method.validation_groups%',
            service('sylius.validator.groups_generator.gateway_config'),
        ])
    ;
    $services->alias(PaymentMethodGroupsGeneratorInterface::class, 'sylius.validator.groups_generator.payment_method');

    $services->set('sylius.generator.gateway_name', GatewayNameGenerator::class);
    $services->alias(GatewayNameGeneratorInterface::class, 'sylius.generator.gateway_name');
};
