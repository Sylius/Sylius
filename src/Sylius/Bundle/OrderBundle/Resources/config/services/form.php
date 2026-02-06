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

use Sylius\Bundle\OrderBundle\Form\DataMapper\OrderItemQuantityDataMapper;
use Sylius\Bundle\OrderBundle\Form\Type\CartItemType;
use Sylius\Bundle\OrderBundle\Form\Type\CartType;
use Sylius\Bundle\OrderBundle\Form\Type\OrderItemType;
use Sylius\Bundle\OrderBundle\Form\Type\OrderType;
use Symfony\Component\Form\Extension\Core\DataMapper\DataMapper;

return static function (ContainerConfigurator $container) {
    $parameters = $container->parameters();
    $services = $container->services();
    $parameters->set('sylius.form.type.order.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.order_item.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.cart.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.cart_item.validation_groups', ['sylius']);

    $services
        ->set('sylius.form.data_mapper.order_item_quantity', OrderItemQuantityDataMapper::class)
        ->args([
            service('sylius.modifier.order_item_quantity'),
            inline_service(DataMapper::class),
        ])
    ;

    $services
        ->set('sylius.form.type.order', OrderType::class)
        ->args([
            '%sylius.model.order.class%',
            '%sylius.form.type.order.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.order_item', OrderItemType::class)
        ->args([
            '%sylius.model.order_item.class%',
            '%sylius.form.type.order_item.validation_groups%',
            service('sylius.form.data_mapper.order_item_quantity'),
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.cart', CartType::class)
        ->args([
            '%sylius.model.order.class%',
            '%sylius.form.type.cart.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.cart_item', CartItemType::class)
        ->args([
            '%sylius.model.order_item.class%',
            '%sylius.form.type.cart_item.validation_groups%',
            service('sylius.form.data_mapper.order_item_quantity'),
        ])
        ->tag('form.type')
    ;
};
