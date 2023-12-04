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
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Webmozart\Assert\Assert;

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
        if (null === $router) {
            trigger_deprecation(
                'sylius/admin-bundle',
                '1.13',
                'Not passing a $router as the fourth argument is deprecated and will be prohibited in Sylius 2.0',
            );
        }
        $this->router = $router;
    }

    public function impersonateAction(Request $request, string $username): Response
    {
        if (!$this->authorizationChecker->isGranted($this->authorizationRole)) {
            throw new HttpException(Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->userProvider->loadUserByUsername($username);
        Assert::isInstanceOf($user, SymfonyUserInterface::class);
        Assert::isInstanceOf($user, UserInterface::class);

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
