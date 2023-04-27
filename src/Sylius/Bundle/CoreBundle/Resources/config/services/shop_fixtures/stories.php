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

use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Story\DefaultCurrenciesStory;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Story\DefaultGeographicalStory;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('sylius.shop_fixtures.story.default_currencies', DefaultCurrenciesStory::class)
            ->tag('foundry.story')
        ->alias(DefaultCurrenciesStory::class, 'sylius.shop_fixtures.foundry.story.default_currencies')

        ->set('sylius.shop_fixtures.story.default_geographical', DefaultGeographicalStory::class)
            ->tag('foundry.story')
        ->alias(DefaultGeographicalStory::class, 'sylius.shop_fixtures.foundry.story.default_geographical')
    ;
};
