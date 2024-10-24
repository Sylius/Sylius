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

namespace Sylius\Bundle\UiBundle\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Allow to redirect already logged-in user to a specific route.
 *
 * To do so, add in your route configuration a 'logged_in_route' attribute under the '_sylius' key.
 * You can provide directly the route name or an array with 'name' and 'parameters' keys.
 */
final class AlreadyLoggedInUserRedirectionListener
{
    public function __construct(
        private AuthorizationCheckerInterface $authorizationChecker,
        private RequestStack $requestStack,
        private RouterInterface $router
    ) {
    }

    public function handleAlreadyConnectedUser(RequestEvent $event): void
    {
        if (!$this->authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return;
        }

        $alreadyLoggedInRedirectRoute = $this->requestStack->getMainRequest()->attributes->get('_sylius')['logged_in_route'] ?? null;
        if (null === $alreadyLoggedInRedirectRoute) {
            return;
        } elseif (is_string($alreadyLoggedInRedirectRoute)) {
            // case logged_in_route: 'app_route'
            $event->setResponse(new RedirectResponse($this->router->generate($alreadyLoggedInRedirectRoute)));
            return;
        }

        // case logged_in_route:
        //     name: 'app_route'
        //     parameters:
        //         param1: 'value1'
        $event->setResponse(new RedirectResponse($this->router->generate(
            $alreadyLoggedInRedirectRoute['name'] ?? throw new \InvalidArgumentException('The "name" key must be set for the "logged_in_route" attribute.'),
            $alreadyLoggedInRedirectRoute['parameters'] ?? []
        )));
    }
}
