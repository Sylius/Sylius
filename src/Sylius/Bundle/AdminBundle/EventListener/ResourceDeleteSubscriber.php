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
use Sylius\Bundle\CoreBundle\Provider\FlashBagProvider;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ResourceDeleteSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UrlGeneratorInterface $router,
        private RequestStack|SessionInterface $requestStackOrSession,
    ) {
        if ($this->requestStackOrSession instanceof SessionInterface) {
            trigger_deprecation(
                'sylius/admin-bundle',
                '1.12',
                'Passing an instance of %s as constructor argument for %s is deprecated and will be removed in Sylius 2.0. Pass an instance of %s instead.',
                SessionInterface::class,
                self::class,
                RequestStack::class,
            );
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onResourceDelete',
        ];
    }

    public function onResourceDelete(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if (!$exception instanceof ForeignKeyConstraintViolationException) {
            return;
        }

        if (\method_exists($event, 'isMainRequest')) {
            $isMainRequest = $event->isMainRequest();
        } else {
            /** @phpstan-ignore-next-line */
            $isMainRequest = $event->isMasterRequest();
        }
        if (!$isMainRequest || 'html' !== $event->getRequest()->getRequestFormat()) {
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

        FlashBagProvider::getFlashBag($this->requestStackOrSession)->add('error', [
            'message' => 'sylius.resource.delete_error',
            'parameters' => ['%resource%' => $resourceName],
        ]);

        $referrer = $eventRequest->headers->get('referer');
        if (null !== $referrer) {
            $event->setResponse(new RedirectResponse($referrer));

            return;
        }

        $event->setResponse($this->createRedirectResponse($originalRoute, ResourceActions::INDEX));
    }

    private function getResourceNameFromRoute(string $route): string
    {
        $route = str_replace('_bulk', '', $route);
        $routeArray = explode('_', $route);
        $routeArrayWithoutAction = array_slice($routeArray, 0, count($routeArray) - 1);
        $routeArrayWithoutPrefixes = array_slice($routeArrayWithoutAction, 2);

        return trim(implode(' ', $routeArrayWithoutPrefixes));
    }

    private function createRedirectResponse(string $originalRoute, string $targetAction): RedirectResponse
    {
        $redirectRoute = str_replace(ResourceActions::DELETE, $targetAction, $originalRoute);

        return new RedirectResponse($this->router->generate($redirectRoute));
    }

    private function isMethodDelete(Request $request): bool
    {
        return Request::METHOD_DELETE === $request->getMethod();
    }

    private function isSyliusRoute(string $route): bool
    {
        return str_starts_with($route, 'sylius');
    }

    private function isAdminSection(array $syliusParameters): bool
    {
        return array_key_exists('section', $syliusParameters) && 'admin' === $syliusParameters['section'];
    }
}
