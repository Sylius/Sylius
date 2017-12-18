<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\FirewallMapInterface;

final class SessionNameSubscriber implements EventSubscriberInterface
{
    /**
     * @var FirewallMapInterface
     */
    private $firewallMap;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @param FirewallMapInterface $firewallMap
     * @param SessionInterface $session
     */
    public function __construct(FirewallMapInterface $firewallMap, SessionInterface $session)
    {
        $this->firewallMap = $firewallMap;
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 15]
        ];
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event): void
    {
        if ( null === $this->firewallMap ) {
            return;
        }

        if ( null === $this->session ) {
            return;
        }

        $firewallConfig = $this->firewallMap->getFirewallConfig($event->getRequest());
        $cookies = $event->getRequest()->cookies;

        if ($cookies->has($this->session->getName())) {
            if($this->session->getId() !== $cookies->get($this->session->getName()) ) {
                $this->session->setName(
                    sprintf('sylius_%s', $firewallConfig->getName())
                );
            }
        }
    }
}
