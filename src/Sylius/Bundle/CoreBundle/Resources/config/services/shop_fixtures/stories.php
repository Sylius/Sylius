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

use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Story\DefaultCurrenciesStory as FoundryDefaultCurrenciesStory;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Story\DefaultGeographicalStory as FoundryDefaultGeographicalStory;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Story\DefaultLocalesStory as FoundryDefaultLocalesStory;
use Sylius\Bundle\CoreBundle\ShopFixtures\Story\DefaultCurrenciesStory;
use Sylius\Bundle\CoreBundle\ShopFixtures\Story\DefaultCurrenciesStoryInterface;
use Sylius\Bundle\CoreBundle\ShopFixtures\Story\DefaultGeographicalStory;
use Sylius\Bundle\CoreBundle\ShopFixtures\Story\DefaultGeographicalStoryInterface;
use Sylius\Bundle\CoreBundle\ShopFixtures\Story\DefaultLocalesStory;

return static function (ContainerConfigurator $container) {
    $container->services()

        ->set('sylius.shop_fixtures.story.default_currencies', DefaultCurrenciesStory::class)
            ->args([
                service('sylius.shop_fixtures.bus.command')
            ])
        ->alias(DefaultCurrenciesStoryInterface::class, 'sylius.shop_fixtures.story.default_currencies')

        ->set('sylius.shop_fixtures.foundry.story.default_currencies', FoundryDefaultCurrenciesStory::class)
            ->args([
                service('sylius.shop_fixtures.story.default_currencies')
            ])
            ->tag('foundry.story')
        ->alias(FoundryDefaultCurrenciesStory::class, 'sylius.shop_fixtures.foundry.story.default_currencies')

        ->set('sylius.shop_fixtures.story.default_geographical', DefaultGeographicalStory::class)
            ->args([
                service('sylius.shop_fixtures.bus.command')
            ])
        ->alias(DefaultGeographicalStoryInterface::class, 'sylius.shop_fixtures.story.default_geographical')

        ->set('sylius.shop_fixtures.foundry.story.default_geographical', FoundryDefaultGeographicalStory::class)
            ->args([
                service('sylius.shop_fixtures.story.default_geographical')
            ])
            ->tag('foundry.story')
        ->alias(FoundryDefaultGeographicalStory::class, 'sylius.shop_fixtures.foundry.story.default_geographical')

        ->set('sylius.shop_fixtures.story.default_locales', DefaultLocalesStory::class)
            ->args([
                service('sylius.shop_fixtures.bus.command')
            ])
        ->alias(DefaultLocalesStory::class, 'sylius.shop_fixtures.story.default_locales')

        ->set('sylius.shop_fixtures.foundry.story.default_locales', FoundryDefaultLocalesStory::class)
            ->args([
                service('sylius.shop_fixtures.story.default_locales')
            ])
            ->tag('foundry.story')
        ->alias(FoundryDefaultLocalesStory::class, 'sylius.shop_fixtures.foundry.story.default_locales')

    ;
};
