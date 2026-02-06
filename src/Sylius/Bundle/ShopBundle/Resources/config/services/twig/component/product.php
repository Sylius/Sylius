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

use Sylius\Bundle\ShopBundle\Form\Type\AddToCartType;
use Sylius\Bundle\ShopBundle\Twig\Component\Product\AddToCartFormComponent;
use Sylius\Bundle\ShopBundle\Twig\Component\Product\AssociationComponent;
use Sylius\Bundle\ShopBundle\Twig\Component\Product\BreadcrumbComponent;
use Sylius\Bundle\ShopBundle\Twig\Component\Product\CardComponent;
use Sylius\Bundle\ShopBundle\Twig\Component\Product\ListComponent;
use Sylius\Bundle\ShopBundle\Twig\Component\Product\PriceComponent;
use Sylius\Bundle\ShopBundle\Twig\Component\Product\SummaryComponent;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius_shop.twig.component.product.add_to_cart_form', AddToCartFormComponent::class)
        ->args([
            service('form.factory'),
            service('doctrine.orm.entity_manager'),
            service('router'),
            service('request_stack'),
            service('event_dispatcher'),
            service('sylius.context.cart'),
            service('sylius.factory.add_to_cart_command'),
            service('sylius.factory.order_item'),
            AddToCartType::class,
            service('sylius.repository.product'),
            service('sylius.repository.product_variant'),
        ])
        ->call('setLiveResponder', [service('ux.live_component.live_responder')])
        ->tag('sylius.live_component.shop', ['key' => 'sylius_shop:product:add_to_cart_form'])
    ;

    $services
        ->set('sylius_shop.twig.component.product.association', AssociationComponent::class)
        ->args([
            service('sylius.repository.product_association'),
            service('sylius.context.channel'),
        ])
        ->tag('sylius.twig_component', ['key' => 'sylius_shop:product:association'])
    ;

    $services
        ->set('sylius_shop.twig.component.product.card', CardComponent::class)
        ->args([
            service('sylius.repository.product'),
            service('sylius.resolver.product_variant'),
            service('sylius.context.channel'),
            service('sylius.context.locale'),
            service('sylius.calculator.product_variant_price'),
        ])
        ->tag('sylius.twig_component', ['key' => 'sylius_shop:product:card'])
    ;

    $services
        ->set('sylius_shop.twig.component.product.list', ListComponent::class)
        ->args([
            service('sylius.repository.product'),
            service('sylius.context.locale'),
            service('sylius.context.channel'),
        ])
        ->tag('sylius.twig_component', ['key' => 'sylius_shop:product:list'])
    ;

    $services
        ->set('sylius_shop.twig.component.product.price', PriceComponent::class)
        ->args([
            service('sylius.calculator.product_variant_price'),
            service('sylius.formatter.money'),
            service('sylius.context.channel'),
            service('sylius.context.locale'),
            service('sylius.context.currency'),
            service('sylius.converter.currency'),
        ])
        ->tag('sylius.twig_component', ['key' => 'sylius_shop:product:price'])
        ->tag('sylius.twig_component', ['key' => 'sylius_shop:product:card:price'])
    ;

    $services
        ->set('sylius_shop.twig.component.product.summary', SummaryComponent::class)
        ->args([
            service('sylius.resolver.product_variant.default'),
            service('sylius.repository.product'),
            service('sylius.repository.product_variant'),
        ])
        ->tag('sylius.live_component.shop', ['key' => 'sylius_shop:product:summary'])
    ;

    $services
        ->set('sylius_shop.twig.component.product.breadcrumb', BreadcrumbComponent::class)
        ->args([
            service('request_stack'),
            service('sylius.repository.taxon'),
            service('sylius.context.locale'),
        ])
        ->tag('sylius.twig_component', ['key' => 'sylius_shop:product:show:breadcrumbs'])
        ->tag('sylius.twig_component', ['key' => 'sylius_shop:product:show:taxonomy'])
        ->tag('sylius.twig_component', ['key' => 'sylius_shop:product:show:header'])
    ;
};
