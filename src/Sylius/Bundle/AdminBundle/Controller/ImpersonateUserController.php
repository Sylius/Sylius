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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class ImpersonateUserController
{
    /**
     * @var UserImpersonatorInterface
     */
    private $impersonator;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var string
     */
    private $authorizationRole;

    /**
     * @param UserImpersonatorInterface $impersonator
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param UserProviderInterface $userProvider
     * @param RouterInterface $router
     * @param string $authorizationRole
     */
    public function __construct(
        UserImpersonatorInterface $impersonator,
        AuthorizationCheckerInterface $authorizationChecker,
        UserProviderInterface $userProvider,
        RouterInterface $router,
        string $authorizationRole
    ) {
        $this->impersonator = $impersonator;
        $this->authorizationChecker = $authorizationChecker;
        $this->userProvider = $userProvider;
        $this->router = $router;
        $this->authorizationRole = $authorizationRole;
    }

    /**
     * @param Request $request
     * @param string $username
     *
     * @return Response
     */
    public function impersonateAction(Request $request, string $username): Response
    {
        if (!$this->authorizationChecker->isGranted($this->authorizationRole)) {
            throw new HttpException(Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->userProvider->loadUserByUsername($username);
        if (null === $user) {
            throw new HttpException(Response::HTTP_NOT_FOUND);
        }

        $this->impersonator->impersonate($user);

        $this->addFlash($request, $username);

        return new RedirectResponse($request->headers->get('referer'));
    }

    /**
     * @param Request $request
     * @param string $username
     */
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
