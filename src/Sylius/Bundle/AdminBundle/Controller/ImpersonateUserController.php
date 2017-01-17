<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ImpersonateUserController
{
    /**
     * @var UserImpersonatorInterface
     */
    protected $impersonator;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var UserProviderInterface
     */
    protected $userProvider;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var string
     */
    protected $authorizationRole;

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
        $authorizationRole
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
    public function impersonateAction(Request $request, $username)
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
    private function addFlash(Request $request, $username)
    {
        /** @var Session $session */
        $session = $request->getSession();
        $session->getFlashBag()->add('success', [
            'message' => 'sylius.customer.impersonate',
            'parameters' => ['%name%' => $username]
        ]);
    }
}
