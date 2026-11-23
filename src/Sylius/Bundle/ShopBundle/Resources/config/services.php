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

use Sylius\Bundle\CoreBundle\Context\SessionAndChannelBasedCartContext;
use Sylius\Bundle\CoreBundle\Storage\CartSessionStorage;
use Sylius\Bundle\GridBundle\Form\Type\Filter\StringFilterType;
use Sylius\Bundle\ShopBundle\SectionResolver\ShopUriBasedSectionResolver;
use Sylius\Component\Core\Storage\CartStorageInterface;
use Sylius\Component\Grid\Filter\StringFilter;

return static function (ContainerConfigurator $container) {
    $container->import('services/**.php');
    $container->import('services/order_pay/**/*.php');
    $container->import('services/twig/**/*.php');

    $services = $container->services();

    $services
        ->set('sylius_shop.section_resolver.shop_uri_based', ShopUriBasedSectionResolver::class)
        ->args(['account'])
        ->tag('sylius.uri_based_section_resolver', ['priority' => -10])
    ;

    $services
        ->set('sylius_shop.context.cart.session_and_channel_based', SessionAndChannelBasedCartContext::class)
        ->args([
            service('sylius_shop.storage.cart_session'),
            service('sylius.context.channel'),
        ])
        ->tag('sylius.context.cart', ['priority' => -777])
    ;

    $services
        ->set('sylius_shop.storage.cart_session', CartSessionStorage::class)
        ->args([
            service('request_stack'),
            '_sylius.cart',
            service('sylius.repository.order'),
        ])
    ;
    $services->alias(CartStorageInterface::class, 'sylius_shop.storage.cart_session');

    $services
        ->set('sylius_shop.grid_filter.string', StringFilter::class)
        ->tag('sylius.grid_filter', ['type' => 'shop_string', 'form_type' => StringFilterType::class])
    ;
};
