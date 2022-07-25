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

namespace Sylius\Bundle\AdminBundle\Controller;

use Sylius\Bundle\CoreBundle\Security\UserImpersonatorInterface;
use Sylius\Bundle\UserBundle\Provider\UserProviderInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class ImpersonateUserController
{
    private ?RouterInterface $router;

    public function __construct(
        private UserImpersonatorInterface $impersonator,
        private AuthorizationCheckerInterface $authorizationChecker,
        private UserProviderInterface $userProvider,
        ?RouterInterface $router,
        private string $authorizationRole,
    ) {
        if (null !== $router) {
            @trigger_error('Passing RouterInterface as the fourth argument is deprecated since 1.4 and will be prohibited in 2.0', \E_USER_DEPRECATED);
        }
        $this->router = $router;
    }

    public function impersonateAction(Request $request, string $username): Response
    {
        if (!$this->authorizationChecker->isGranted($this->authorizationRole)) {
            throw new HttpException(Response::HTTP_UNAUTHORIZED);
        }

        /** @var UserInterface $user */
        $user = $this->userProvider->loadUserByUsername($username);

        $this->impersonator->impersonate($user);

        $this->addFlash($request, $username);

        $redirectUrl = $request->headers->get(
            'referer',
            $this->router->generate('sylius_admin_customer_show', ['id' => $user->getId()]),
        );

        return new RedirectResponse($redirectUrl);
    }

    private function addFlash(Request $request, string $username): void
    {
        /** @var Session $session */
        $session = $request->getSession();
        $session->getFlashBag()->add('success', [
            'message' => 'sylius.customer.impersonate',
            'parameters' => ['%name%' => $username],
        ]);
    }
}
