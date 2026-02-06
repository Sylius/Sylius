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

use Sylius\Bundle\CoreBundle\Form\Type\Checkout\AddressType;
use Sylius\Bundle\CoreBundle\Form\Type\Checkout\ChangePaymentMethodType;
use Sylius\Bundle\CoreBundle\Form\Type\Checkout\CompleteType;
use Sylius\Bundle\CoreBundle\Form\Type\Checkout\PaymentType;
use Sylius\Bundle\CoreBundle\Form\Type\Checkout\SelectPaymentType;
use Sylius\Bundle\CoreBundle\Form\Type\Checkout\SelectShippingType;
use Sylius\Bundle\CoreBundle\Form\Type\Checkout\ShipmentType;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $parameters->set('sylius.form.type.checkout_address.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.checkout_select_shipping.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.checkout_shipment.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.checkout_select_payment.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.checkout_payment.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.checkout_complete.validation_groups', ['sylius', 'sylius_checkout_complete']);

    $services->set('sylius.form.type.checkout.address', AddressType::class)
        ->args([
            service('sylius.comparator.address'),
            '%sylius.model.order.class%',
            '%sylius.form.type.checkout_address.validation_groups%',
        ])
        ->tag('form.type');

    $services->set('sylius.form.type.checkout.select_shipping', SelectShippingType::class)
        ->args([
            '%sylius.model.order.class%',
            '%sylius.form.type.checkout_select_shipping.validation_groups%',
        ])
        ->tag('form.type');

    $services->set('sylius.form.type.checkout.shipment', ShipmentType::class)
        ->args([
            '%sylius.model.shipment.class%',
            '%sylius.form.type.checkout_shipment.validation_groups%',
        ])
        ->tag('form.type');

    $services->set('sylius.form.type.checkout.select_payment', SelectPaymentType::class)
        ->args([
            '%sylius.model.order.class%',
            '%sylius.form.type.checkout_select_payment.validation_groups%',
        ])
        ->tag('form.type');

    $services->set('sylius.form.type.checkout.payment', PaymentType::class)
        ->args([
            '%sylius.model.payment.class%',
            '%sylius.form.type.checkout_payment.validation_groups%',
        ])
        ->tag('form.type');

    $services->set('sylius.form.type.checkout.complete', CompleteType::class)
        ->args([
            '%sylius.model.order.class%',
            '%sylius.form.type.checkout_complete.validation_groups%',
        ])
        ->tag('form.type');

    $services->set('sylius.form.type.checkout.change_payment_method', ChangePaymentMethodType::class)
        ->args(['%sylius_core.checkout.payment.allowed_states%'])
        ->tag('form.type');
};
