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

namespace Sylius\Bundle\ShopBundle\EventListener;

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class AlreadyLoggedInUserRegistrationListener implements EventSubscriberInterface
{
    private AuthorizationCheckerInterface $authorizationChecker;

    private RequestStack $requestStack;

    private RouterInterface $router;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        RequestStack $requestStack,
        RouterInterface $router
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->requestStack = $requestStack;
        $this->router = $router;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.customer.initialize_register' => 'handleAlreadyConnectedUser',
        ];
    }

    public function handleAlreadyConnectedUser(ResourceControllerEvent $event): void
    {
        if (!$this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            // Not authenticated, nothing to do here
            return;
        }

        $alreadyLoggedInRedirectRoute = $this->requestStack->getMainRequest()->attributes->get('_sylius')['logged_in_route'] ?? null;
        if (null !== $alreadyLoggedInRedirectRoute) {
            $event->setResponse(new RedirectResponse($this->router->generate($alreadyLoggedInRedirectRoute)));
        }
    }
}
