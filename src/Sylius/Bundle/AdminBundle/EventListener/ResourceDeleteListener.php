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

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Sylius\Component\Core\Exception\ResourceDeleteException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

final readonly class ResourceDeleteListener
{
    public function onResourceDelete(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if (!$exception instanceof ForeignKeyConstraintViolationException) {
            return;
        }

        if (!$event->isMainRequest() || 'html' !== $event->getRequest()->getRequestFormat()) {
            return;
        }

        $eventRequest = $event->getRequest();
        $requestAttributes = $eventRequest->attributes;
        $originalRoute = $requestAttributes->get('_route', '');

        if (!$this->isMethodDelete($eventRequest) ||
            !$this->isSyliusRoute($originalRoute) ||
            !$this->isAdminSection($requestAttributes->get('_sylius', []))
        ) {
            return;
        }

        $resourceName = $this->getResourceNameFromRoute($originalRoute);

        if (null === $requestAttributes->get('_controller')) {
            return;
        }

        throw new ResourceDeleteException($resourceName);
    }

    private function getResourceNameFromRoute(string $route): string
    {
        $route = str_replace('_bulk', '', $route);
        $routeArray = explode('_', $route);
        $routeArrayWithoutAction = array_slice($routeArray, 0, count($routeArray) - 1);
        $routeArrayWithoutPrefixes = array_slice($routeArrayWithoutAction, 2);

        return trim(implode(' ', $routeArrayWithoutPrefixes));
    }

    private function isMethodDelete(Request $request): bool
    {
        return Request::METHOD_DELETE === $request->getMethod();
    }

    private function isSyliusRoute(string $route): bool
    {
        return str_starts_with($route, 'sylius');
    }

    /** @param array<string, mixed> $syliusParameters */
    private function isAdminSection(array $syliusParameters): bool
    {
        return array_key_exists('section', $syliusParameters) && 'admin' === $syliusParameters['section'];
    }
}
