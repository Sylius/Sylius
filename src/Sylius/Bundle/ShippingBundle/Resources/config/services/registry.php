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

use Sylius\Bundle\ResourceBundle\Form\Registry\FormTypeRegistry;
use Sylius\Component\Registry\PrioritizedServiceRegistry;
use Sylius\Component\Registry\ServiceRegistry;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Sylius\Component\Shipping\Checker\Rule\RuleCheckerInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.registry.shipping_calculator', ServiceRegistry::class)
        ->args([
            CalculatorInterface::class,
            'shipping calculator',
        ])
    ;

    $services
        ->set('sylius.registry.shipping_methods_resolver', PrioritizedServiceRegistry::class)
        ->args([
            '%sylius.shipping_methods_resolver.interface%',
            'Shipping methods resolver',
        ])
    ;

    $services
        ->set('sylius.registry.shipping_method_rule_checker', ServiceRegistry::class)
        ->args([
            RuleCheckerInterface::class,
            'shipping method rule checker',
        ])
    ;

    $services->set('sylius.form_registry.shipping_method_rule_checker', FormTypeRegistry::class);
};
