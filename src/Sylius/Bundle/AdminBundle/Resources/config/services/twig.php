<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $container->import('twig/**/*.php');


    $services->set('sylius_admin.twig.extension.string_loader', 'Twig\Extension\StringLoaderExtension')
        ->tag('twig.extension');

    $services->set('sylius_admin.twig.extension.attribute', 'Sylius\Bundle\AdminBundle\Twig\AttributeExtension')
        ->args([service('sylius.registry.attribute_type')])
        ->tag('twig.extension');

    $services->set('sylius_admin.twig.extension.shop', 'Sylius\Bundle\AdminBundle\Twig\ShopExtension')
        ->args(['%sylius.admin.shop_enabled%'])
        ->tag('twig.extension');

    $services->set('sylius_admin.twig.extension.channels_currencies', 'Sylius\Bundle\AdminBundle\Twig\ChannelsCurrenciesExtension')
        ->args([service('sylius.repository.channel')])
        ->tag('twig.extension');

    $services->set('sylius_admin.twig.extension.order_unit_taxes', 'Sylius\Bundle\AdminBundle\Twig\OrderUnitTaxesExtension')
        ->tag('twig.extension');

    $services->set('sylius_admin.twig.extension.channel_name', 'Sylius\Bundle\AdminBundle\Twig\ChannelNameExtension')
        ->args([service('sylius.repository.channel')])
        ->tag('twig.extension');

    $services->set('sylius_admin.twig.extension.payment_method', 'Sylius\Bundle\AdminBundle\Twig\PaymentMethodExtension')
        ->args([
            '%sylius.gateway_factories%',
            '%sylius.admin.twig.payment_method.excluded_gateways%',
        ])
        ->tag('twig.extension');

    $services->set('sylius_admin.twig.extension.promotion_labels', 'Sylius\Bundle\AdminBundle\Twig\PromotionLabelsExtension')
        ->args([
            '%sylius.promotion_rules%',
            '%sylius.promotion_actions%',
        ])
        ->tag('twig.extension');
};
