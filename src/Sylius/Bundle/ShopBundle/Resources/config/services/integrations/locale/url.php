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

use Sylius\Bundle\ShopBundle\EventListener\NonChannelLocaleListener;
use Sylius\Bundle\ShopBundle\Locale\UrlBasedLocaleSwitcher;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_shop.locale_switcher', UrlBasedLocaleSwitcher::class)
        ->args([service('router')]);

    $services->set('sylius_shop.listener.non_channel_locale', NonChannelLocaleListener::class)
        ->public()
        ->args([
            service('router'),
            service('sylius.provider.locale'),
            service('security.firewall.map'),
            ['%sylius_shop.firewall_context_name%'],
        ])
        ->tag('kernel.event_listener', ['event' => 'kernel.request', 'method' => 'restrictRequestLocale', 'priority' => 10]);
};
