<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $parameters->set('sylius.form.type.checkout_address.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.checkout_select_shipping.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.checkout_shipment.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.checkout_select_payment.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.checkout_payment.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.checkout_complete.validation_groups', ['sylius', 'sylius_checkout_complete']);

    $services->set('sylius.form.type.checkout.address', 'Sylius\Bundle\CoreBundle\Form\Type\Checkout\AddressType')
        ->args([
            service('sylius.comparator.address'),
            '%sylius.model.order.class%',
            '%sylius.form.type.checkout_address.validation_groups%',
        ])
        ->tag('form.type');

    $services->set('sylius.form.type.checkout.select_shipping', 'Sylius\Bundle\CoreBundle\Form\Type\Checkout\SelectShippingType')
        ->args([
            '%sylius.model.order.class%',
            '%sylius.form.type.checkout_select_shipping.validation_groups%',
        ])
        ->tag('form.type');

    $services->set('sylius.form.type.checkout.shipment', 'Sylius\Bundle\CoreBundle\Form\Type\Checkout\ShipmentType')
        ->args([
            '%sylius.model.shipment.class%',
            '%sylius.form.type.checkout_shipment.validation_groups%',
        ])
        ->tag('form.type');

    $services->set('sylius.form.type.checkout.select_payment', 'Sylius\Bundle\CoreBundle\Form\Type\Checkout\SelectPaymentType')
        ->args([
            '%sylius.model.order.class%',
            '%sylius.form.type.checkout_select_payment.validation_groups%',
        ])
        ->tag('form.type');

    $services->set('sylius.form.type.checkout.payment', 'Sylius\Bundle\CoreBundle\Form\Type\Checkout\PaymentType')
        ->args([
            '%sylius.model.payment.class%',
            '%sylius.form.type.checkout_payment.validation_groups%',
        ])
        ->tag('form.type');

    $services->set('sylius.form.type.checkout.complete', 'Sylius\Bundle\CoreBundle\Form\Type\Checkout\CompleteType')
        ->args([
            '%sylius.model.order.class%',
            '%sylius.form.type.checkout_complete.validation_groups%',
        ])
        ->tag('form.type');

    $services->set('sylius.form.type.checkout.change_payment_method', 'Sylius\Bundle\CoreBundle\Form\Type\Checkout\ChangePaymentMethodType')
        ->args(['%sylius_core.checkout.payment.allowed_states%'])
        ->tag('form.type');
};
