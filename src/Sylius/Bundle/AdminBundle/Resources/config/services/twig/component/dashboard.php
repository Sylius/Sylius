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

use Sylius\Bundle\AdminBundle\Twig\Component\Dashboard\ChannelSelectorComponent;
use Sylius\Bundle\AdminBundle\Twig\Component\Dashboard\NewCustomersComponent;
use Sylius\Bundle\AdminBundle\Twig\Component\Dashboard\NewOrdersComponent;
use Sylius\Bundle\AdminBundle\Twig\Component\Dashboard\StatisticsComponent;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius_admin.twig.component.dashboard.channel_selector', ChannelSelectorComponent::class)
        ->args([service('sylius.repository.channel')])
        ->call('setLiveResponder', [service('ux.live_component.live_responder')])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:dashboard:channel_selector'])
    ;

    $services
        ->set('sylius_admin.twig.component.dashboard.new_customers', NewCustomersComponent::class)
        ->args([service('sylius.repository.customer')])
        ->tag('sylius.twig_component', ['key' => 'sylius_admin:dashboard:new_customers'])
    ;

    $services
        ->set('sylius_admin.twig.component.dashboard.new_orders', NewOrdersComponent::class)
        ->args([
            service('sylius.repository.order'),
            service('sylius.repository.channel'),
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:dashboard:new_orders'])
    ;

    $services
        ->set('sylius_admin.twig.component.dashboard.statistics', StatisticsComponent::class)
        ->args([
            service('sylius.repository.channel'),
            service('sylius.provider.statistics'),
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:dashboard:statistics'])
    ;
};
