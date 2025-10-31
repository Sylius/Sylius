<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.calculator.shipping.flat_rate', 'Sylius\Component\Core\Shipping\Calculator\FlatRateCalculator')
        ->tag('sylius.shipping_calculator', ['calculator' => 'flat_rate', 'form_type' => 'Sylius\Bundle\CoreBundle\Form\Type\Shipping\Calculator\ChannelBasedFlatRateConfigurationType', 'label' => 'sylius.form.shipping_calculator.flat_rate_configuration.label']);

    $services->set('sylius.calculator.shipping.per_unit_rate', 'Sylius\Component\Core\Shipping\Calculator\PerUnitRateCalculator')
        ->tag('sylius.shipping_calculator', ['calculator' => 'per_unit_rate', 'form_type' => 'Sylius\Bundle\CoreBundle\Form\Type\Shipping\Calculator\ChannelBasedPerUnitRateConfigurationType', 'label' => 'sylius.form.shipping_calculator.per_unit_rate_configuration.label']);

    $services->set('sylius.checker.shipping_method_rule.order_total_greater_than_or_equal', 'Sylius\Component\Core\Shipping\Checker\Rule\OrderTotalGreaterThanOrEqualRuleChecker')
        ->tag('sylius.shipping_method_rule_checker', ['type' => 'order_total_greater_than_or_equal', 'label' => 'sylius.form.shipping_method_rule.items_total_greater_than_or_equal', 'form_type' => 'Sylius\Bundle\CoreBundle\Form\Type\Shipping\Rule\ChannelBasedOrderTotalGreaterThanOrEqualConfigurationType']);

    $services->set('sylius.checker.shipping_method_rule.order_total_less_than_or_equal', 'Sylius\Component\Core\Shipping\Checker\Rule\OrderTotalLessThanOrEqualRuleChecker')
        ->tag('sylius.shipping_method_rule_checker', ['type' => 'order_total_less_than_or_equal', 'label' => 'sylius.form.shipping_method_rule.items_total_less_than_or_equal', 'form_type' => 'Sylius\Bundle\CoreBundle\Form\Type\Shipping\Rule\ChannelBasedOrderTotalLessThanOrEqualConfigurationType']);
};
