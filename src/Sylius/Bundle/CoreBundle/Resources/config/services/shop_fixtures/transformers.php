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

use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Transformer\CountryTransformer;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Transformer\CountryTransformerInterface;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Transformer\CurrencyTransformer;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Transformer\CurrencyTransformerInterface;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Transformer\LocaleTransformer;
use Sylius\Bundle\CoreBundle\ShopFixtures\Foundry\Transformer\LocaleTransformerInterface;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('sylius.shop_fixtures.transformer.country', CountryTransformer::class)
        ->alias(CountryTransformerInterface::class, 'sylius.shop_fixtures.transformer.country')

        ->set('sylius.shop_fixtures.transformer.currency', CurrencyTransformer::class)
        ->alias(CurrencyTransformerInterface::class, 'sylius.shop_fixtures.transformer.currency')

        ->set('sylius.shop_fixtures.transformer.locale', LocaleTransformer::class)
        ->alias(LocaleTransformerInterface::class, 'sylius.shop_fixtures.transformer.locale')
    ;
};
