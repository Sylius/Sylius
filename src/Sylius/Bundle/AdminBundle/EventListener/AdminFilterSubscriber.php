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

namespace Sylius\Bundle\AdminBundle\EventListener;

use Sylius\Bundle\UiBundle\Storage\FilterStorageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class AdminFilterSubscriber implements EventSubscriberInterface
{
    public function __construct(private FilterStorageInterface $filterStorage)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$this->isMainRequest($event)) {
            return;
        }

        $eventRequest = $event->getRequest();

        if ('html' !== $eventRequest->getRequestFormat()) {
            return;
        }

        $requestAttributes = $eventRequest->attributes;

        if (
            null === $requestAttributes->get('_controller') ||
            !$this->isIndexResourceRoute($requestAttributes->get('_route', '')) ||
            !$this->isAdminSection($requestAttributes->get('_sylius', []))
        ) {
            return;
        }

        if ($this->filterStorage->all() !== $eventRequest->query->all()) {
            $this->filterStorage->set($eventRequest->query->all());
        }
    }

    private function isMainRequest(RequestEvent $event): bool
    {
        if (\method_exists($event, 'isMainRequest')) {
            return $event->isMainRequest();
        }

        /** @phpstan-ignore-next-line */
        return $event->isMasterRequest();
    }

    private function isIndexResourceRoute(string $route): bool
    {
        return str_ends_with($route, 'index');
    }

    private function isAdminSection(array $syliusParameters): bool
    {
        return isset($syliusParameters['section']) && 'admin' === $syliusParameters['section'];
    }
}
