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

use Sylius\Bundle\ShopBundle\Form\Type\CartType;
use Sylius\Bundle\ShopBundle\Twig\Component\Cart\FormComponent;
use Sylius\Bundle\ShopBundle\Twig\Component\Cart\SummaryComponent;
use Sylius\Bundle\ShopBundle\Twig\Component\Cart\WidgetComponent;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_shop.twig.component.cart.form', FormComponent::class)
        ->args([
            service('sylius.repository.order'),
            service('form.factory'),
            '%sylius.model.order.class%',
            CartType::class,
            service('doctrine.orm.entity_manager'),
            service('event_dispatcher'),
        ])
        ->call('setLiveResponder', [service('ux.live_component.live_responder')])
        ->tag('sylius.live_component.shop', ['key' => 'sylius_shop:cart:form']);

    $services->set('sylius_shop.twig.component.cart.summary', SummaryComponent::class)
        ->args([service('sylius.repository.order')])
        ->tag('sylius.live_component.shop', ['key' => 'sylius_shop:cart:summary']);

    $services->set('sylius_shop.twig.component.cart.widget', WidgetComponent::class)
        ->args([
            service('sylius.context.cart'),
            service('sylius.repository.order'),
        ])
        ->tag('sylius.live_component.shop', ['key' => 'sylius_shop:cart:widget']);

    $services->set('sylius_shop.twig.component.cart.widget.offcanvas', WidgetComponent::class)
        ->args([
            service('sylius.context.cart'),
            service('sylius.repository.order'),
        ])
        ->tag('sylius.live_component.shop', ['key' => 'sylius_shop:cart:widget:offcanvas']);
};
