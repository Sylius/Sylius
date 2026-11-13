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

use Sylius\Bundle\CoreBundle\Context\CustomerAndChannelBasedCartContext;
use Sylius\Bundle\LocaleBundle\Context\RequestBasedLocaleContext;
use Sylius\Component\Channel\Context\CachedPerRequestChannelContext;
use Sylius\Component\Core\Cart\Context\ShopBasedCartContext;
use Sylius\Component\Core\Context\ShopperContext;
use Sylius\Component\Core\Context\ShopperContextInterface;
use Sylius\Component\Core\Currency\Context\ChannelAwareCurrencyContext;
use Sylius\Component\Core\Currency\Context\StorageBasedCurrencyContext;
use Sylius\Component\Core\Currency\CurrencyStorage;
use Sylius\Component\Core\Currency\CurrencyStorageInterface;
use Sylius\Component\Core\Provider\ChannelBasedLocaleProvider;
use Sylius\Component\Locale\Context\ProviderBasedLocaleContext;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.context.channel.cached', CachedPerRequestChannelContext::class)
        ->decorate('sylius.context.channel', null, 256)
        ->args([
            service('sylius.context.channel.cached.inner'),
            service('request_stack'),
        ])
    ;

    $services
        ->set('sylius.context.shopper', ShopperContext::class)
        ->args([
            service('sylius.context.channel'),
            service('sylius.context.currency'),
            service('sylius.context.locale'),
            service('sylius.context.customer'),
        ])
        ->lazy()
    ;
    $services->alias(ShopperContextInterface::class, 'sylius.context.shopper');

    $services
        ->set('sylius.context.cart.new_shop_based', ShopBasedCartContext::class)
        ->decorate('sylius.context.cart.new', null, 256)
        ->args([
            service('sylius.context.cart.new_shop_based.inner'),
            service('sylius.context.shopper'),
            service('sylius.resolver.cart.created_by_guest_flag'),
        ])
    ;

    $services
        ->set('sylius.context.cart.customer_and_channel_based', CustomerAndChannelBasedCartContext::class)
        ->args([
            service('sylius.context.customer'),
            service('sylius.context.channel'),
            service('sylius.repository.order'),
        ])
        ->tag('sylius.context.cart', ['priority' => -555])
    ;

    $services->set('sylius.storage.currency', CurrencyStorage::class)->args([service('sylius.storage.cookie')]);
    $services->alias(CurrencyStorageInterface::class, 'sylius.storage.currency');

    $services
        ->set('sylius.context.currency.storage_based', StorageBasedCurrencyContext::class)
        ->args([
            service('sylius.context.channel'),
            service('sylius.storage.currency'),
        ])
        ->tag('sylius.context.currency')
    ;

    $services
        ->set('sylius.context.currency.channel_aware', ChannelAwareCurrencyContext::class)
        ->decorate('sylius.context.currency', null, 256)
        ->args([
            service('sylius.context.currency.channel_aware.inner'),
            service('sylius.context.channel'),
        ])
    ;

    $services
        ->set('sylius.provider.locale.channel_based', ChannelBasedLocaleProvider::class)
        ->decorate('sylius.provider.locale', null, 256)
        ->args([
            service('sylius.context.channel'),
            '%locale%',
        ])
    ;

    $services
        ->set('sylius.context.locale.request_based', RequestBasedLocaleContext::class)
        ->args([
            service('request_stack'),
            service('sylius.provider.locale'),
        ])
        ->tag('sylius.context.locale', ['priority' => 64])
    ;

    $services
        ->set('sylius.context.locale.provider_based', ProviderBasedLocaleContext::class)
        ->args([service('sylius.provider.locale')])
        ->tag('sylius.context.locale', ['priority' => -128])
    ;
};
