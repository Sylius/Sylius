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

use Sylius\Bundle\CoreBundle\Form\Type\Shipping\Calculator\ChannelBasedFlatRateConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\Shipping\Calculator\ChannelBasedPerUnitRateConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\Shipping\Rule\ChannelBasedOrderTotalGreaterThanOrEqualConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\Shipping\Rule\ChannelBasedOrderTotalLessThanOrEqualConfigurationType;
use Sylius\Component\Core\Shipping\Calculator\FlatRateCalculator;
use Sylius\Component\Core\Shipping\Calculator\PerUnitRateCalculator;
use Sylius\Component\Core\Shipping\Checker\Rule\OrderTotalGreaterThanOrEqualRuleChecker;
use Sylius\Component\Core\Shipping\Checker\Rule\OrderTotalLessThanOrEqualRuleChecker;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.calculator.shipping.flat_rate', FlatRateCalculator::class)
        ->tag('sylius.shipping_calculator', ['calculator' => 'flat_rate', 'form_type' => ChannelBasedFlatRateConfigurationType::class, 'label' => 'sylius.form.shipping_calculator.flat_rate_configuration.label']);

    $services->set('sylius.calculator.shipping.per_unit_rate', PerUnitRateCalculator::class)
        ->tag('sylius.shipping_calculator', ['calculator' => 'per_unit_rate', 'form_type' => ChannelBasedPerUnitRateConfigurationType::class, 'label' => 'sylius.form.shipping_calculator.per_unit_rate_configuration.label']);

    $services->set('sylius.checker.shipping_method_rule.order_total_greater_than_or_equal', OrderTotalGreaterThanOrEqualRuleChecker::class)
        ->tag('sylius.shipping_method_rule_checker', ['type' => 'order_total_greater_than_or_equal', 'label' => 'sylius.form.shipping_method_rule.items_total_greater_than_or_equal', 'form_type' => ChannelBasedOrderTotalGreaterThanOrEqualConfigurationType::class]);

    $services->set('sylius.checker.shipping_method_rule.order_total_less_than_or_equal', OrderTotalLessThanOrEqualRuleChecker::class)
        ->tag('sylius.shipping_method_rule_checker', ['type' => 'order_total_less_than_or_equal', 'label' => 'sylius.form.shipping_method_rule.items_total_less_than_or_equal', 'form_type' => ChannelBasedOrderTotalLessThanOrEqualConfigurationType::class]);
};
