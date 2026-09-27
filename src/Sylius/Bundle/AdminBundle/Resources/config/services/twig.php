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

use Sylius\Bundle\AdminBundle\Provider\PaymentGatewayMethodsProvider;
use Sylius\Bundle\AdminBundle\Provider\PaymentGatewayMethodsProviderInterface;
use Sylius\Bundle\AdminBundle\Twig\AttributeExtension;
use Sylius\Bundle\AdminBundle\Twig\ChannelNameExtension;
use Sylius\Bundle\AdminBundle\Twig\ChannelsCurrenciesExtension;
use Sylius\Bundle\AdminBundle\Twig\OrderUnitTaxesExtension;
use Sylius\Bundle\AdminBundle\Twig\PaymentGatewayMethodsExtension;
use Sylius\Bundle\AdminBundle\Twig\PaymentMethodExtension;
use Sylius\Bundle\AdminBundle\Twig\PaymentMethodGatewayFactoryExtension;
use Sylius\Bundle\AdminBundle\Twig\PromotionLabelsExtension;
use Sylius\Bundle\AdminBundle\Twig\ShopExtension;
use Sylius\Component\Payment\Checker\PaymentMethodGatewayFactoryCheckerInterface;
use Twig\Extension\StringLoaderExtension;

return static function (ContainerConfigurator $container) {
    $container->import('twig/**/*.php');

    $services = $container->services();

    $services->set('sylius_admin.twig.extension.string_loader', StringLoaderExtension::class)->tag('twig.extension');

    $services
        ->set('sylius_admin.twig.extension.attribute', AttributeExtension::class)
        ->args([service('sylius.registry.attribute_type')])
        ->tag('twig.extension')
    ;

    $services
        ->set('sylius_admin.twig.extension.shop', ShopExtension::class)
        ->args(['%sylius.admin.shop_enabled%'])
        ->tag('twig.extension')
    ;

    $services
        ->set('sylius_admin.twig.extension.channels_currencies', ChannelsCurrenciesExtension::class)
        ->args([service('sylius.repository.channel')])
        ->tag('twig.extension')
    ;

    $services
        ->set('sylius_admin.twig.extension.order_unit_taxes', OrderUnitTaxesExtension::class)
        ->tag('twig.extension')
    ;

    $services
        ->set('sylius_admin.twig.extension.channel_name', ChannelNameExtension::class)
        ->args([service('sylius.repository.channel')])
        ->tag('twig.extension')
    ;

    $services
        ->set('sylius_admin.twig.extension.payment_method', PaymentMethodExtension::class)
        ->args([
            '%sylius.gateway_factories%',
            '%sylius.admin.twig.payment_method.excluded_gateways%',
        ])
        ->tag('twig.extension')
    ;

    $services
        ->set('sylius_admin.twig.extension.payment_method_gateway_factory', PaymentMethodGatewayFactoryExtension::class)
        ->args([service(PaymentMethodGatewayFactoryCheckerInterface::class)])
        ->tag('twig.extension')
    ;

    $services
        ->set('sylius_admin.provider.payment_gateway_methods', PaymentGatewayMethodsProvider::class)
        ->args([
            [
                'adyen' => ['visa', 'master_card', 'amex', 'alipay', 'wechat_pay', 'klarna'],
                'mollie' => ['visa', 'master_card', 'maestro', 'ideal', 'bancontact', 'blik'],
                'paypal' => ['visa', 'master_card', 'amex', 'paypal', 'venmo', 'apple_pay'],
                'stripe' => ['visa', 'master_card', 'amex', 'apple_pay', 'google_pay', 'klarna'],
            ],
            [
                'paypal_express_checkout' => 'paypal',
                'stripe_checkout' => 'stripe',
                'stripe_web_element' => 'stripe',
            ],
        ])
    ;
    $services->alias(PaymentGatewayMethodsProviderInterface::class, 'sylius_admin.provider.payment_gateway_methods');

    $services
        ->set('sylius_admin.twig.extension.payment_gateway_methods', PaymentGatewayMethodsExtension::class)
        ->args([service(PaymentGatewayMethodsProviderInterface::class)])
        ->tag('twig.extension')
    ;

    $services
        ->set('sylius_admin.twig.extension.promotion_labels', PromotionLabelsExtension::class)
        ->args([
            '%sylius.promotion_rules%',
            '%sylius.promotion_actions%',
        ])
        ->tag('twig.extension')
    ;
};
