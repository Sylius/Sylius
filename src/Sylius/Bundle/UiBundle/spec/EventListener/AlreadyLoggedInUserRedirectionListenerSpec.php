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

namespace spec\Sylius\Bundle\UiBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


final class AlreadyLoggedInUserRedirectionListenerSpec extends ObjectBehavior
{
    function let(
        AuthorizationCheckerInterface $authorizationChecker,
        RequestStack $requestStack,
        RouterInterface $router
    ): void {
        $this->beConstructedWith($authorizationChecker, $requestStack, $router);
    }

    function it_add_response_to_event_when_user_is_already_logged_in(
        RequestEvent $event,
        Request $request,
        ParameterBag $requestAttributes,
        AuthorizationCheckerInterface $authorizationChecker,
        RequestStack $requestStack,
        RouterInterface $router
    ): void {
        $authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')->willReturn(true);

        $requestStack->getMainRequest()->willReturn($request);
        $request->attributes = $requestAttributes;
        $requestAttributes->get('_sylius')->willReturn(['logged_in_route' => 'app_route']);

        $router->generate('app_route')->willReturn('/app_route');

        $event->setResponse(new RedirectResponse('/app_route'))->shouldBeCalled();

        $this->handleAlreadyConnectedUser($event);
    }

    function it_add_response_to_event_when_user_is_already_logged_in_with_parameters(
        RequestEvent $event,
        Request $request,
        ParameterBag $requestAttributes,
        AuthorizationCheckerInterface $authorizationChecker,
        RequestStack $requestStack,
        RouterInterface $router
    ): void {
        $authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')->willReturn(true);

        $requestStack->getMainRequest()->willReturn($request);
        $request->attributes = $requestAttributes;
        $requestAttributes->get('_sylius')->willReturn([
            'logged_in_route' => [
                'name' => 'app_route',
                'parameters' => ['param1' => 'value1']
            ]
        ]);

        $router->generate('app_route', ['param1' => 'value1'])->willReturn('/app_route?param1=value1');

        $event->setResponse(new RedirectResponse('/app_route?param1=value1'))->shouldBeCalled();

        $this->handleAlreadyConnectedUser($event);
    }

    function it_does_not_alter_event_when_not_logged_in(
        RequestEvent $event,
        AuthorizationCheckerInterface $authorizationChecker
    ): void {
        $authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')->willReturn(false);

        $event->setResponse(new RedirectResponse('/app_route'))->shouldNotBeCalled();

        $this->handleAlreadyConnectedUser($event);
    }
}
