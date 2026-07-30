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

use Sylius\Component\Registry\ServiceRegistry;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Calculator\DecimalCalculator;
use Sylius\Component\Taxation\Calculator\DefaultCalculator;
use Sylius\Component\Taxation\Calculator\DelegatingCalculator;
use Sylius\Component\Taxation\Checker\TaxRateDateEligibilityChecker;
use Sylius\Component\Taxation\Checker\TaxRateDateEligibilityCheckerInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolver;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;

return static function (ContainerConfigurator $container) {
    $container->import('services/form.php');

    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.tax_calculator.interface', CalculatorInterface::class);

    $services
        ->set('sylius.registry.tax_calculator', ServiceRegistry::class)
        ->args([
            '%sylius.tax_calculator.interface%',
            'Tax calculator',
        ])
    ;

    $services
        ->set('sylius.tax_calculator', DelegatingCalculator::class)
        ->args([service('sylius.registry.tax_calculator')])
    ;
    $services->alias(CalculatorInterface::class, 'sylius.tax_calculator');

    $services
        ->set('sylius.tax_calculator.default', DefaultCalculator::class)
        ->tag('sylius.tax_calculator', ['calculator' => 'default'])
    ;

    $services
        ->set('sylius.tax_calculator.decimal', DecimalCalculator::class)
        ->tag('sylius.tax_calculator', ['calculator' => 'decimal'])
    ;

    $services
        ->set('sylius.resolver.tax_rate', TaxRateResolver::class)
        ->args([
            service('sylius.repository.tax_rate'),
            service('sylius.checker.tax_rate_date_eligibility')->nullOnInvalid(),
        ])
    ;
    $services->alias(TaxRateResolverInterface::class, 'sylius.resolver.tax_rate');

    $services
        ->set('sylius.checker.tax_rate_date_eligibility', TaxRateDateEligibilityChecker::class)
        ->args([service('clock')])
    ;
    $services->alias(TaxRateDateEligibilityCheckerInterface::class, 'sylius.checker.tax_rate_date_eligibility');
};
