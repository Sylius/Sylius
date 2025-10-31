<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $parameters->set('sylius.order_payment_processor.checkout.unsupported_states', [\Sylius\Component\Order\Model\OrderInterface::STATE_CANCELLED, \Sylius\Component\Order\Model\OrderInterface::STATE_FULFILLED]);
    $parameters->set('sylius.order_payment_processor.after_checkout.unsupported_states', [\Sylius\Component\Order\Model\OrderInterface::STATE_CANCELLED, \Sylius\Component\Order\Model\OrderInterface::STATE_FULFILLED]);
    $parameters->set('sylius.order_processing.adjustment_clearing_types', [\Sylius\Component\Core\Model\AdjustmentInterface::ORDER_ITEM_PROMOTION_ADJUSTMENT, \Sylius\Component\Core\Model\AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, \Sylius\Component\Core\Model\AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT, \Sylius\Component\Core\Model\AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT, \Sylius\Component\Core\Model\AdjustmentInterface::SHIPPING_ADJUSTMENT, \Sylius\Component\Core\Model\AdjustmentInterface::TAX_ADJUSTMENT]);

    $services->set('sylius.order_processing.order_adjustments_clearer', 'Sylius\Component\Core\OrderProcessing\OrderAdjustmentsClearer')
        ->args(['%sylius.order_processing.adjustment_clearing_types%'])
        ->tag('sylius.order_processor', ['priority' => 60]);

    $services->set('sylius.order_processing.order_prices_recalculator', 'Sylius\Component\Core\OrderProcessing\OrderPricesRecalculator')
        ->args([service('sylius.calculator.product_variant_price')])
        ->tag('sylius.order_processor', ['priority' => 50]);

    $services->set('sylius.order_processing.order_shipment_processor', 'Sylius\Component\Core\OrderProcessing\OrderShipmentProcessor')
        ->args([
            service('sylius.resolver.shipping_method.default'),
            service('sylius.factory.shipment'),
            service('sylius.resolver.shipping_methods'),
        ])
        ->tag('sylius.order_processor', ['priority' => 40]);

    $services->set('sylius.order_processing.shipping_charges_processor', 'Sylius\Component\Core\OrderProcessing\ShippingChargesProcessor')
        ->args([
            service('sylius.factory.adjustment'),
            service('sylius.calculator.shipping'),
        ])
        ->tag('sylius.order_processor', ['priority' => 30]);

    $services->set('sylius.order_processing.order_promotion_processor', 'Sylius\Component\Core\OrderProcessing\OrderPromotionProcessor')
        ->args([service('sylius.processor.promotion')])
        ->tag('sylius.order_processor', ['priority' => 20]);

    $services->set('sylius.order_processing.order_taxes_processor', 'Sylius\Component\Core\OrderProcessing\OrderTaxesProcessor')
        ->args([
            service('sylius.provider.channel_based_default_zone'),
            service('sylius.matcher.zone'),
            service('sylius.registry.tax_calculation_strategy'),
            service('sylius.resolver.taxation_address'),
        ])
        ->tag('sylius.order_processor', ['priority' => 10]);

    $services->set('sylius.order_processing.order_payment_processor.checkout', 'Sylius\Component\Core\OrderProcessing\OrderPaymentProcessor')
        ->args([
            service('sylius.provider.payment.order'),
            service('sylius.remover.payment.order'),
            '%sylius.order_payment_processor.checkout.unsupported_states%',
            'cart',
        ])
        ->tag('sylius.order_processor', ['priority' => 0]);

    $services->set('sylius.order_processing.order_payment_processor.after_checkout', 'Sylius\Component\Core\OrderProcessing\OrderPaymentProcessor')
        ->public()
        ->args([
            service('sylius.provider.payment.order'),
            service('sylius.remover.payment.order'),
            '%sylius.order_payment_processor.after_checkout.unsupported_states%',
            'new',
        ]);
};
