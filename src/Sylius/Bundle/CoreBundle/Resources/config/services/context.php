<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.context.channel.cached', 'Sylius\Component\Channel\Context\CachedPerRequestChannelContext')
        ->decorate('sylius.context.channel', null, 256)
        ->args([
            service('sylius.context.channel.cached.inner'),
            service('request_stack'),
        ]);

    $services->set('sylius.context.shopper', 'Sylius\Component\Core\Context\ShopperContext')
        ->lazy()
        ->args([
            service('sylius.context.channel'),
            service('sylius.context.currency'),
            service('sylius.context.locale'),
            service('sylius.context.customer'),
        ]);

    $services->alias('Sylius\Component\Core\Context\ShopperContextInterface', 'sylius.context.shopper');

    $services->set('sylius.context.cart.new_shop_based', 'Sylius\Component\Core\Cart\Context\ShopBasedCartContext')
        ->decorate('sylius.context.cart.new', null, 256)
        ->args([
            service('sylius.context.cart.new_shop_based.inner'),
            service('sylius.context.shopper'),
            service('sylius.resolver.cart.created_by_guest_flag'),
        ]);

    $services->set('sylius.context.cart.customer_and_channel_based', 'Sylius\Bundle\CoreBundle\Context\CustomerAndChannelBasedCartContext')
        ->args([
            service('sylius.context.customer'),
            service('sylius.context.channel'),
            service('sylius.repository.order'),
        ])
        ->tag('sylius.context.cart', ['priority' => -555]);

    $services->set('sylius.storage.currency', 'Sylius\Component\Core\Currency\CurrencyStorage')
        ->args([service('sylius.storage.cookie')]);

    $services->alias('Sylius\Component\Core\Currency\CurrencyStorageInterface', 'sylius.storage.currency');

    $services->set('sylius.context.currency.storage_based', 'Sylius\Component\Core\Currency\Context\StorageBasedCurrencyContext')
        ->args([
            service('sylius.context.channel'),
            service('sylius.storage.currency'),
        ])
        ->tag('sylius.context.currency');

    $services->set('sylius.context.currency.channel_aware', 'Sylius\Component\Core\Currency\Context\ChannelAwareCurrencyContext')
        ->decorate('sylius.context.currency', null, 256)
        ->args([
            service('sylius.context.currency.channel_aware.inner'),
            service('sylius.context.channel'),
        ]);

    $services->set('sylius.provider.locale.channel_based', 'Sylius\Component\Core\Provider\ChannelBasedLocaleProvider')
        ->decorate('sylius.provider.locale', null, 256)
        ->args([
            service('sylius.context.channel'),
            '%locale%',
        ]);

    $services->set('sylius.context.locale.request_based', 'Sylius\Bundle\LocaleBundle\Context\RequestBasedLocaleContext')
        ->args([
            service('request_stack'),
            service('sylius.provider.locale'),
        ])
        ->tag('sylius.context.locale', ['priority' => 64]);

    $services->set('sylius.context.locale.provider_based', 'Sylius\Component\Locale\Context\ProviderBasedLocaleContext')
        ->args([service('sylius.provider.locale')])
        ->tag('sylius.context.locale', ['priority' => -128]);
};
