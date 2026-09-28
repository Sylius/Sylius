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

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\OrderProcessing\OrderAdjustmentsClearer;
use Sylius\Component\Core\OrderProcessing\OrderItemUnitAdjustmentsPreloader;
use Sylius\Component\Core\OrderProcessing\OrderPaymentProcessor;
use Sylius\Component\Core\OrderProcessing\OrderPricesRecalculator;
use Sylius\Component\Core\OrderProcessing\OrderPromotionProcessor;
use Sylius\Component\Core\OrderProcessing\OrderShipmentProcessor;
use Sylius\Component\Core\OrderProcessing\OrderTaxesProcessor;
use Sylius\Component\Core\OrderProcessing\ShippingChargesProcessor;
use Sylius\Component\Order\Model\OrderInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.order_payment_processor.checkout.unsupported_states', [OrderInterface::STATE_CANCELLED, OrderInterface::STATE_FULFILLED]);
    $parameters->set('sylius.order_payment_processor.after_checkout.unsupported_states', [OrderInterface::STATE_CANCELLED, OrderInterface::STATE_FULFILLED]);
    $parameters->set('sylius.order_processing.adjustment_clearing_types', [AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT, AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT, AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT, AdjustmentInterface::SHIPPING_ADJUSTMENT, AdjustmentInterface::TAX_ADJUSTMENT]);

    $services
        ->set('sylius.order_processing.order_item_unit_adjustments_preloader', OrderItemUnitAdjustmentsPreloader::class)
        ->args([service('sylius.repository.order_item_unit')])
        ->tag('sylius.order_processor', ['priority' => 70])
    ;

    $services
        ->set('sylius.order_processing.order_adjustments_clearer', OrderAdjustmentsClearer::class)
        ->args(['%sylius.order_processing.adjustment_clearing_types%'])
        ->tag('sylius.order_processor', ['priority' => 60])
    ;

    $services
        ->set('sylius.order_processing.order_prices_recalculator', OrderPricesRecalculator::class)
        ->args([service('sylius.calculator.product_variant_price')])
        ->tag('sylius.order_processor', ['priority' => 50])
    ;

    $services
        ->set('sylius.order_processing.order_shipment_processor', OrderShipmentProcessor::class)
        ->args([
            service('sylius.resolver.shipping_method.default'),
            service('sylius.factory.shipment'),
            service('sylius.resolver.shipping_methods'),
        ])
        ->tag('sylius.order_processor', ['priority' => 40])
    ;

    $services
        ->set('sylius.order_processing.shipping_charges_processor', ShippingChargesProcessor::class)
        ->args([
            service('sylius.factory.adjustment'),
            service('sylius.calculator.shipping'),
        ])
        ->tag('sylius.order_processor', ['priority' => 30])
    ;

    $services
        ->set('sylius.order_processing.order_promotion_processor', OrderPromotionProcessor::class)
        ->args([service('sylius.processor.promotion')])
        ->tag('sylius.order_processor', ['priority' => 20])
    ;

    $services
        ->set('sylius.order_processing.order_taxes_processor', OrderTaxesProcessor::class)
        ->args([
            service('sylius.provider.channel_based_default_zone'),
            service('sylius.matcher.zone'),
            service('sylius.registry.tax_calculation_strategy'),
            service('sylius.resolver.taxation_address'),
        ])
        ->tag('sylius.order_processor', ['priority' => 10])
    ;

    $services
        ->set('sylius.order_processing.order_payment_processor.checkout', OrderPaymentProcessor::class)
        ->args([
            service('sylius.provider.payment.order'),
            service('sylius.remover.payment.order'),
            '%sylius.order_payment_processor.checkout.unsupported_states%',
            'cart',
        ])
        ->tag('sylius.order_processor', ['priority' => 0])
    ;

    $services
        ->set('sylius.order_processing.order_payment_processor.after_checkout', OrderPaymentProcessor::class)
        ->args([
            service('sylius.provider.payment.order'),
            service('sylius.remover.payment.order'),
            '%sylius.order_payment_processor.after_checkout.unsupported_states%',
            'new',
        ])
        ->public()
    ;
};
