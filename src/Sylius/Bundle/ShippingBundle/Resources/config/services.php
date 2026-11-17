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

use Sylius\Bundle\ShippingBundle\Assigner\ShippingDateAssigner;
use Sylius\Bundle\ShippingBundle\Form\Type\Calculator\FlatRateConfigurationType;
use Sylius\Bundle\ShippingBundle\Form\Type\Calculator\PerUnitRateConfigurationType;
use Sylius\Bundle\ShippingBundle\Form\Type\Rule\TotalWeightGreaterThanOrEqualConfigurationType;
use Sylius\Bundle\ShippingBundle\Form\Type\Rule\TotalWeightLessThanOrEqualConfigurationType;
use Sylius\Component\Shipping\Calculator\DelegatingCalculator;
use Sylius\Component\Shipping\Calculator\DelegatingCalculatorInterface;
use Sylius\Component\Shipping\Calculator\FlatRateCalculator;
use Sylius\Component\Shipping\Calculator\PerUnitRateCalculator;
use Sylius\Component\Shipping\Checker\Rule\TotalWeightGreaterThanOrEqualRuleChecker;
use Sylius\Component\Shipping\Checker\Rule\TotalWeightLessThanOrEqualRuleChecker;
use Sylius\Component\Shipping\Resolver\CompositeMethodsResolver;
use Sylius\Component\Shipping\Resolver\DefaultShippingMethodResolver;
use Sylius\Component\Shipping\Resolver\DefaultShippingMethodResolverInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolver;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;

return static function (ContainerConfigurator $container) {
    $container->import('services/**.php');

    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.shipping_methods_resolver.interface', ShippingMethodsResolverInterface::class);

    $services
        ->set('sylius.resolver.shipping_methods', CompositeMethodsResolver::class)
        ->args([service('sylius.registry.shipping_methods_resolver')])
    ;
    $services->alias('sylius.resolver.shipping_methods.composite', 'sylius.resolver.shipping_methods');

    $services->alias(ShippingMethodsResolverInterface::class, 'sylius.resolver.shipping_methods');

    $services
        ->set('sylius.resolver.shipping_methods.default', ShippingMethodsResolver::class)
        ->args([
            service('sylius.repository.shipping_method'),
            service('sylius.checker.shipping_method_eligibility'),
        ])
        ->tag('sylius.shipping_method_resolver', ['type' => 'default', 'label' => 'Default'])
    ;

    $services
        ->set('sylius.resolver.shipping_method.default', DefaultShippingMethodResolver::class)
        ->args([service('sylius.repository.shipping_method')])
    ;
    $services->alias(DefaultShippingMethodResolverInterface::class, 'sylius.resolver.shipping_method.default');

    $services
        ->set('sylius.calculator.shipping', DelegatingCalculator::class)
        ->args([service('sylius.registry.shipping_calculator')])
    ;
    $services->alias(DelegatingCalculatorInterface::class, 'sylius.calculator.shipping');

    $services
        ->set('sylius.calculator.shipping.flat_rate', FlatRateCalculator::class)
        ->tag('sylius.shipping_calculator', ['calculator' => 'flat_rate', 'form_type' => FlatRateConfigurationType::class, 'label' => 'sylius.form.shipping_calculator.flat_rate_configuration.label'])
    ;

    $services
        ->set('sylius.calculator.shipping.per_unit_rate', PerUnitRateCalculator::class)
        ->tag('sylius.shipping_calculator', ['calculator' => 'per_unit_rate', 'form_type' => PerUnitRateConfigurationType::class, 'label' => 'sylius.form.shipping_calculator.per_unit_rate_configuration.label'])
    ;

    $services
        ->set('sylius.assigner.shipping_date', ShippingDateAssigner::class)
        ->args([service('clock')])
        ->public()
    ;

    $services
        ->set('sylius.checker.shipping_method_rule.total_weight_greater_than_or_equal', TotalWeightGreaterThanOrEqualRuleChecker::class)
        ->tag('sylius.shipping_method_rule_checker', ['type' => 'total_weight_greater_than_or_equal', 'label' => 'sylius.form.shipping_method_rule.total_weight_greater_than_or_equal', 'form_type' => TotalWeightGreaterThanOrEqualConfigurationType::class])
    ;

    $services
        ->set('sylius.checker.shipping_method_rule.total_weight_less_than_or_equal', TotalWeightLessThanOrEqualRuleChecker::class)
        ->tag('sylius.shipping_method_rule_checker', ['type' => 'total_weight_less_than_or_equal', 'label' => 'sylius.form.shipping_method_rule.total_weight_less_than_or_equal', 'form_type' => TotalWeightLessThanOrEqualConfigurationType::class])
    ;
};
