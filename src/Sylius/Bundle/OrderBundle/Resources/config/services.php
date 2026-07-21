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

use Sylius\Bundle\OrderBundle\Adder\CartItemAdder;
use Sylius\Bundle\OrderBundle\Adder\CartItemAdderInterface;
use Sylius\Bundle\OrderBundle\Console\Command\RemoveExpiredCartsCommand;
use Sylius\Bundle\OrderBundle\Factory\AddToCartCommandFactory;
use Sylius\Bundle\OrderBundle\Factory\AddToCartCommandFactoryInterface;
use Sylius\Bundle\OrderBundle\NumberAssigner\OrderNumberAssigner;
use Sylius\Bundle\OrderBundle\NumberAssigner\OrderNumberAssignerInterface;
use Sylius\Bundle\OrderBundle\NumberGenerator\OrderNumberGeneratorInterface;
use Sylius\Bundle\OrderBundle\NumberGenerator\SequentialOrderNumberGenerator;
use Sylius\Bundle\OrderBundle\Remover\ExpiredCartsRemover;
use Sylius\Bundle\OrderBundle\Resetter\CartChangesResetter;
use Sylius\Bundle\OrderBundle\Resetter\CartChangesResetterInterface;
use Sylius\Component\Order\Aggregator\AdjustmentsAggregatorInterface;
use Sylius\Component\Order\Aggregator\AdjustmentsByLabelAggregator;
use Sylius\Component\Order\Context\CartContext;
use Sylius\Component\Order\Context\CompositeCartContext;
use Sylius\Component\Order\Factory\AdjustmentFactory;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifier;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifier;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Sylius\Component\Order\Processor\CompositeOrderProcessor;
use Sylius\Component\Order\Remover\ExpiredCartsRemoverInterface;

return static function (ContainerConfigurator $container) {
    $container->import('services/form.php');
    $container->import('services/twig.php');

    $services = $container->services();

    $services
        ->set('sylius.console.command.remove_expired_carts', RemoveExpiredCartsCommand::class)
        ->args([
            service('sylius.remover.expired_carts'),
            '%sylius_order.cart_expiration_period%',
        ])
        ->public()
        ->tag('console.command')
    ;

    $services
        ->set('sylius.context.cart.new', CartContext::class)
        ->args([service('sylius.factory.order')])
        ->tag('sylius.context.cart', ['priority' => -999])
    ;

    $services->set('sylius.context.cart.composite', CompositeCartContext::class);

    $services->set('sylius.order_processing.order_processor.composite', CompositeOrderProcessor::class);

    $services
        ->set('sylius.modifier.order', OrderModifier::class)
        ->args([
            service('sylius.order_processing.order_processor'),
            service('sylius.modifier.order_item_quantity'),
        ])
    ;
    $services->alias(OrderModifierInterface::class, 'sylius.modifier.order');

    $services
        ->set('sylius.modifier.order_item_quantity', OrderItemQuantityModifier::class)
        ->args([service('sylius.factory.order_item_unit')])
    ;
    $services->alias(OrderItemQuantityModifierInterface::class, 'sylius.modifier.order_item_quantity');

    $services
        ->set('sylius.number_generator.sequential_order', SequentialOrderNumberGenerator::class)
        ->args([
            service('sylius.repository.order_sequence'),
            service('sylius.factory.order_sequence'),
        ])
    ;
    $services->alias(OrderNumberGeneratorInterface::class, 'sylius.number_generator.sequential_order');

    $services
        ->set('sylius.number_assigner.order_number', OrderNumberAssigner::class)
        ->args([service('sylius.number_generator.sequential_order')])
        ->public()
    ;
    $services->alias(OrderNumberAssignerInterface::class, 'sylius.number_assigner.order_number')->public();

    $services->set('sylius.aggregator.adjustments_by_label', AdjustmentsByLabelAggregator::class);
    $services->alias(AdjustmentsAggregatorInterface::class, 'sylius.aggregator.adjustments_by_label');

    $services
        ->set('sylius.custom_factory.adjustment', AdjustmentFactory::class)
        ->decorate('sylius.factory.adjustment', null, 256)
        ->args([service('sylius.custom_factory.adjustment.inner')])
        ->private()
    ;

    $services->alias(AdjustmentFactoryInterface::class, 'sylius.factory.adjustment');

    $services
        ->set('sylius.remover.expired_carts', ExpiredCartsRemover::class)
        ->args([
            service('sylius.repository.order'),
            service('sylius.manager.order'),
            service('event_dispatcher'),
            '%sylius_order.cart_expiration_period%',
        ])
    ;
    $services->alias(ExpiredCartsRemoverInterface::class, 'sylius.remover.expired_carts');

    $services->set('sylius.factory.add_to_cart_command', AddToCartCommandFactory::class);
    $services->alias(AddToCartCommandFactoryInterface::class, 'sylius.factory.add_to_cart_command');

    $services
        ->set('sylius.adder.cart_item', CartItemAdder::class)
        ->args([
            service('sylius.factory.add_to_cart_command'),
            service('event_dispatcher'),
            service('sylius.manager.order'),
        ])
    ;
    $services->alias(CartItemAdderInterface::class, 'sylius.adder.cart_item');

    $services
        ->set('sylius.resetter.cart_changes', CartChangesResetter::class)
        ->args([service('sylius.manager.order')])
        ->public()
    ;
    $services->alias(CartChangesResetterInterface::class, 'sylius.resetter.cart_changes');
};
