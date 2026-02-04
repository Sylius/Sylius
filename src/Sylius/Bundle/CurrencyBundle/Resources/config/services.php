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

use Sylius\Bundle\CurrencyBundle\Twig\CurrencyExtension;
use Sylius\Bundle\CurrencyBundle\Validator\Constraints\DifferentSourceTargetCurrencyValidator;
use Sylius\Bundle\CurrencyBundle\Validator\Constraints\UniqueCurrencyPairValidator;
use Sylius\Component\Currency\Context\CompositeCurrencyContext;
use Sylius\Component\Currency\Converter\CurrencyConverter;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Sylius\Component\Currency\Converter\CurrencyNameConverter;
use Sylius\Component\Currency\Converter\CurrencyNameConverterInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $container->import('services/form.php');

    $services->set('sylius.context.currency.composite', CompositeCurrencyContext::class)
        ->decorate('sylius.context.currency', null, 256);

    $services->set('sylius.converter.currency', CurrencyConverter::class)
        ->args([service('sylius.repository.exchange_rate')]);

    $services->alias(CurrencyConverterInterface::class, 'sylius.converter.currency');

    $services->set('sylius.converter.currency_name', CurrencyNameConverter::class);

    $services->alias(CurrencyNameConverterInterface::class, 'sylius.converter.currency_name');

    $services->set('sylius.twig.extension.currency', CurrencyExtension::class)
        ->tag('twig.extension');

    $services->set('sylius.validator.different_source_target_currency', DifferentSourceTargetCurrencyValidator::class)
        ->tag('validator.constraint_validator');

    $services->set('sylius.validator.unique_currency_pair', UniqueCurrencyPairValidator::class)
        ->args([service('sylius.repository.exchange_rate')])
        ->tag('validator.constraint_validator');
};
