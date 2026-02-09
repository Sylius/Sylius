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

use Sylius\Bundle\CurrencyBundle\Form\Type\CurrencyChoiceType;
use Sylius\Bundle\CurrencyBundle\Form\Type\CurrencyType;
use Sylius\Bundle\CurrencyBundle\Form\Type\ExchangeRateType;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.form.type.currency.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.exchange_rate.validation_groups', ['sylius']);

    $services
        ->set('sylius.form.type.currency', CurrencyType::class)
        ->args([
            '%sylius.model.currency.class%',
            '%sylius.form.type.currency.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.currency_choice', CurrencyChoiceType::class)
        ->args([service('sylius.repository.currency')])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.exchange_rate', ExchangeRateType::class)
        ->args([
            '%sylius.model.exchange_rate.class%',
            '%sylius.form.type.exchange_rate.validation_groups%',
        ])
        ->tag('form.type')
    ;
};
