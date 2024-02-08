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

namespace Sylius\Bundle\ShopBundle\DependencyInjection\Compiler;

use Sylius\Bundle\ShopBundle\EventListener\ShopUserLogoutHandler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Security\Http\Event\LogoutEvent;

final class LogoutListenerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('sylius_shop.firewall_context_name')) {
            return;
        }

        $firewallName = (string) $container->getParameter('sylius_shop.firewall_context_name');
        $securityDispatcherId = sprintf('security.event_dispatcher.%s', $firewallName);

        if (!$container->hasDefinition($securityDispatcherId)) {
            return;
        }

        $logoutListener = new Definition(ShopUserLogoutHandler::class, [
            new Reference('sylius.context.channel.composite'),
            new Reference('sylius.storage.cart_session'),
        ]);
        $logoutListener->addTag('kernel.event_listener', [
            'event' => LogoutEvent::class,
            'dispatcher' => $securityDispatcherId,
            'method' => 'onLogout',
        ]);
        $logoutListener->setPublic(true);

        $container->setDefinition('sylius.handler.shop_user_logout', $logoutListener);
    }
}
