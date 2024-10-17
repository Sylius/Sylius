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

namespace Sylius\Bundle\CoreBundle\EventListener;

use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

final class UserImpersonatorSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private FirewallMap $firewallMap,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            LogoutEvent::class => 'unimpersonate',
        ];
    }

    public function unimpersonate(LogoutEvent $event): void
    {
        $request = $event->getRequest();
        $config = $this->firewallMap->getFirewallConfig($request);

        if (!$config) {
            return;
        }

        $request->getSession()->remove(
            sprintf('_security_impersonate_sylius_%s', $config->getName()),
        );
    }
}
