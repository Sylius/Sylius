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

use Sylius\Bundle\ShippingBundle\Validator\GroupsGenerator\ShippingMethodConfigurationGroupsGenerator;
use Sylius\Bundle\ShippingBundle\Validator\ShippingMethodCalculatorExistsValidator;
use Sylius\Bundle\ShippingBundle\Validator\ShippingMethodRuleValidator;
use Sylius\Bundle\ShippingBundle\Validator\ValidDeliveryTimeRangeValidator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.validator.shipping_method_calculator_exists', ShippingMethodCalculatorExistsValidator::class)
        ->args(['%sylius.shipping_calculators%'])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_shipping_method_calculator_exists'])
    ;

    $services
        ->set('sylius.validator.shipping_method_rule', ShippingMethodRuleValidator::class)
        ->args([
            '%sylius.shipping_method_rules%',
            '%sylius.shipping.shipping_method_rule.validation_groups%',
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_shipping_method_rule'])
    ;

    $services
        ->set('sylius.validator.groups_generator.shipping_method_configuration', ShippingMethodConfigurationGroupsGenerator::class)
        ->args(['%sylius.shipping.shipping_method_calculator.validation_groups%'])
    ;

    $services
        ->set('sylius.validator.shipping_method_valid_delivery_time_range', ValidDeliveryTimeRangeValidator::class)
        ->tag('validator.constraint_validator', ['alias' => 'sylius_shipping_method_valid_delivery_time_range'])
    ;
};
