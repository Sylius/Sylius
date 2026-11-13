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

use Sylius\Component\Shipping\Checker\Eligibility\CategoryRequirementEligibilityChecker;
use Sylius\Component\Shipping\Checker\Eligibility\CompositeShippingMethodEligibilityChecker;
use Sylius\Component\Shipping\Checker\Eligibility\ShippingMethodEligibilityCheckerInterface;
use Sylius\Component\Shipping\Checker\Eligibility\ShippingMethodRulesEligibilityChecker;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.checker.shipping_method.category_requirement_eligibility', CategoryRequirementEligibilityChecker::class)
        ->tag('sylius.shipping_method_eligibility_checker')
    ;

    $services
        ->set('sylius.checker.shipping_method.rules_eligibility', ShippingMethodRulesEligibilityChecker::class)
        ->args([service('sylius.registry.shipping_method_rule_checker')])
        ->tag('sylius.shipping_method_eligibility_checker')
    ;

    $services
        ->set('sylius.checker.shipping_method_eligibility', CompositeShippingMethodEligibilityChecker::class)
        ->args([[]])
    ;
    $services->alias('sylius.checker.shipping_method_eligibility.composite', 'sylius.checker.shipping_method_eligibility');

    $services->alias(ShippingMethodEligibilityCheckerInterface::class, 'sylius.checker.shipping_method_eligibility');
};
