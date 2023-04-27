<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\DefaultValues\CountryDefaultValues;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\DefaultValues\CountryDefaultValuesInterface;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\DefaultValues\CurrencyDefaultValues;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\DefaultValues\CurrencyDefaultValuesInterface;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\DefaultValues\LocaleDefaultValues;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\DefaultValues\LocaleDefaultValuesInterface;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('sylius.shop_fixtures.default_values.country', CountryDefaultValues::class)
        ->alias(CountryDefaultValuesInterface::class, 'sylius.shop_fixtures.default_values.country')

        ->set('sylius.shop_fixtures.default_values.currency', CurrencyDefaultValues::class)
        ->alias(CurrencyDefaultValuesInterface::class, 'sylius.shop_fixtures.default_values.currency')

        ->set('sylius.shop_fixtures.default_values.locale', LocaleDefaultValues::class)
        ->alias(LocaleDefaultValuesInterface::class, 'sylius.shop_fixtures.default_values.locale')
    ;
};
