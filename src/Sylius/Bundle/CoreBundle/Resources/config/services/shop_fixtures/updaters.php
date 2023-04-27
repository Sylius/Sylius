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

use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Updater\CountryUpdater;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Updater\CountryUpdaterInterface;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Updater\CurrencyUpdater;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Updater\CurrencyUpdaterInterface;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Updater\LocaleUpdater;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Updater\LocaleUpdaterInterface;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('sylius.shop_fixtures.updater.country', CountryUpdater::class)
        ->alias(CountryUpdaterInterface::class, 'sylius.shop_fixtures.updater.country')

        ->set('sylius.shop_fixtures.updater.currency', CurrencyUpdater::class)
        ->alias(CurrencyUpdaterInterface::class, 'sylius.shop_fixtures.updater.currency')

        ->set('sylius.shop_fixtures.updater.locale', LocaleUpdater::class)
        ->alias(LocaleUpdaterInterface::class, 'sylius.shop_fixtures.updater.locale')
    ;
};
