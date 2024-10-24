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

use Sylius\Bundle\CoreBundle\Provider\FlashBagProvider;
use Sylius\Component\Core\Exception\ResourceDeleteException;
use Sylius\Resource\ResourceActions;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final readonly class ResourceDeleteExceptionListener
{
    public function __construct(
        private UrlGeneratorInterface $router,
        private RequestStack $requestStack,
    ) {
    }

    public function onResourceDeleteException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof ResourceDeleteException) {
            return;
        }

        $eventRequest = $event->getRequest();
        if ($eventRequest->attributes->has('_api_operation')) {
            return;
        }

        FlashBagProvider::getFlashBag($this->requestStack)->add('error', [
            'message' => 'sylius.resource.delete_error',
            'parameters' => ['%resource%' => $exception->getResourceName()],
        ]);

        $requestAttributes = $eventRequest->attributes;
        $originalRoute = $requestAttributes->get('_route', '');

        $referrer = $eventRequest->headers->get('referer');
        if (null !== $referrer) {
            $event->setResponse(new RedirectResponse($referrer));

            return;
        }

        $event->setResponse($this->createRedirectResponse($originalRoute, ResourceActions::INDEX));
    }

    private function createRedirectResponse(string $originalRoute, string $targetAction): RedirectResponse
    {
        $redirectRoute = str_replace(ResourceActions::DELETE, $targetAction, $originalRoute);

        return new RedirectResponse($this->router->generate($redirectRoute));
    }
}
