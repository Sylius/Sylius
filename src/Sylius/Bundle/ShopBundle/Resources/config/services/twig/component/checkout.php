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

use Sylius\Bundle\ShopBundle\Form\Type\Checkout\AddressType;
use Sylius\Bundle\ShopBundle\Form\Type\Checkout\SelectPaymentType;
use Sylius\Bundle\ShopBundle\Form\Type\Checkout\SelectShippingType;
use Sylius\Bundle\ShopBundle\Twig\Component\Checkout\Address\AddressBookComponent;
use Sylius\Bundle\ShopBundle\Twig\Component\Checkout\Address\FormComponent;
use Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponent;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_shop.twig.component.checkout.address.form', FormComponent::class)
        ->args([
            service('sylius.repository.order'),
            service('form.factory'),
            '%sylius.model.order.class%',
            AddressType::class,
            service('sylius.context.customer'),
            service('sylius.repository.shop_user'),
            service('sylius.repository.address'),
            tagged_iterator('sylius_shop.modifier.address_form_values'),
        ])
        ->tag('sylius.live_component.shop', ['key' => 'sylius_shop:checkout:address:form']);

    $services->set('sylius_shop.twig.component.checkout.address.address_book', AddressBookComponent::class)
        ->args([service('sylius.context.customer')])
        ->call('setLiveResponder', [service('ux.live_component.live_responder')])
        ->tag('sylius.live_component.shop', ['key' => 'sylius_shop:checkout:address:address_book']);

    $services->set('sylius_shop.twig.component.checkout.shipping.form', ResourceFormComponent::class)
        ->args([
            service('sylius.repository.order'),
            service('form.factory'),
            '%sylius.model.order.class%',
            SelectShippingType::class,
        ])
        ->tag('sylius.live_component.shop', ['key' => 'sylius_shop:checkout:shipping:form']);

    $services->set('sylius_shop.twig.component.checkout.payment.form', ResourceFormComponent::class)
        ->args([
            service('sylius.repository.order'),
            service('form.factory'),
            '%sylius.model.order.class%',
            SelectPaymentType::class,
        ])
        ->tag('sylius.live_component.shop', ['key' => 'sylius_shop:checkout:payment:form']);
};
