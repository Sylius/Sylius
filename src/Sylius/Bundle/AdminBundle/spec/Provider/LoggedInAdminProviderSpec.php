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

namespace spec\Sylius\Bundle\AdminBundle\Provider;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\AdminBundle\Provider\LoggedInUserProviderInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class LoggedInAdminProviderSpec extends ObjectBehavior
{
    private const SECURITY_SESSION_KEY = '_security_admin';
    private const SERIALIZED_TOKEN = 'O:74:"Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken":3:{i:0;N;i:1;s:5:"admin";i:2;a:5:{i:0;O:37:"Sylius\Component\Core\Model\AdminUser":8:{i:0;s:38:"sylius{x33enl1y0askgkgw8k0skocc4ko0kg}";i:1;s:30:"x33enl1y0askgkgw8k0skocc4ko0kg";i:2;s:6:"sylius";i:3;s:6:"sylius";i:4;b:0;i:5;b:1;i:6;i:404;i:7;s:9:"plaintext";}i:1;b:1;i:2;N;i:3;a:0:{}i:4;a:1:{i:0;s:26:"ROLE_ADMINISTRATION_ACCESS";}}}';

    function let(
        Security $security,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack,
        UserRepositoryInterface $adminUserRepository,
    ) {
        $this->beConstructedWith($security, $tokenStorage, $requestStack, $adminUserRepository);
    }

    function it_implements_logged_in_user_provider(): void
    {
        $this->shouldImplement(LoggedInUserProviderInterface::class);
    }

    function it_returns_true_when_user_is_in_security(
        Security $security,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack,
        AdminUserInterface $adminUser,
    ): void {
        $security->getUser()->willReturn($adminUser);

        $tokenStorage->getToken()->shouldNotBeCalled();
        $requestStack->getMainRequest()->shouldNotBeCalled();
        $requestStack->getSession()->shouldNotBeCalled();

        $this->hasUser()->shouldReturn(true);
    }

    function it_returns_true_when_user_is_in_token_storage(
        Security $security,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack,
        TokenInterface $token,
        AdminUserInterface $adminUser,
    ): void {
        $security->getUser()->willReturn(null);

        $token->getUser()->willReturn($adminUser);
        $tokenStorage->getToken()->willReturn($token);

        $requestStack->getMainRequest()->shouldNotBeCalled();
        $requestStack->getSession()->shouldNotBeCalled();

        $this->hasUser()->shouldReturn(true);
    }

    function it_returns_true_when_user_is_in_main_request_session_token(
        Security $security,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack,
        Request $request,
        Session $session,
        TokenInterface $token,
        AdminUserInterface $adminUser,
    ): void {
        $security->getUser()->willReturn(null);
        $tokenStorage->getToken()->willReturn(null);

        $token->getUser()->willReturn($adminUser);
        $session->get(self::SECURITY_SESSION_KEY)->willReturn('serialized_token');
        $request->getSession()->willReturn($session);

        $requestStack->getMainRequest()->willReturn($request);
        $requestStack->getSession()->shouldNotBeCalled();

        $this->hasUser()->shouldReturn(true);
    }

    function it_returns_true_when_user_is_in_current_request_session_token(
        Security $security,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack,
        Session $session,
        TokenInterface $token,
        AdminUserInterface $adminUser,
    ): void {
        $security->getUser()->willReturn(null);
        $tokenStorage->getToken()->willReturn(null);

        $token->getUser()->willReturn($adminUser);
        $session->get(self::SECURITY_SESSION_KEY)->willReturn('serialized_token');

        $requestStack->getMainRequest()->willReturn(null);
        $requestStack->getSession()->willReturn($session);

        $this->hasUser()->shouldReturn(true);
    }

    function it_returns_false_when_there_is_no_user(
        Security $security,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack,
        Session $session,
    ): void {
        $security->getUser()->willReturn(null);
        $tokenStorage->getToken()->willReturn(null);

        $requestStack->getMainRequest()->willReturn(null);

        $session->get(self::SECURITY_SESSION_KEY)->willReturn(null);
        $requestStack->getSession()->willReturn($session);

        $this->hasUser()->shouldReturn(false);
    }

    function it_returns_false_when_user_cannot_be_provided_and_session_is_not_available_in_current_request(
        Security $security,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack,
    ): void {
        $security->getUser()->willReturn(null);
        $tokenStorage->getToken()->willReturn(null);

        $requestStack->getMainRequest()->willReturn(null);
        $requestStack->getSession()->willThrow(SessionNotFoundException::class);

        $this->hasUser()->shouldReturn(false);
    }

    function it_returns_false_when_user_cannot_be_provided_and_session_is_not_available_in_main_request(
        Security $security,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack,
        Request $request,
    ): void {
        $security->getUser()->willReturn(null);
        $tokenStorage->getToken()->willReturn(null);

        $requestStack->getMainRequest()->willReturn($request);
        $request->getSession()->willThrow(SessionNotFoundException::class);

        $this->hasUser()->shouldReturn(false);
    }

    function it_gets_user_from_security(
        Security $security,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack,
        UserRepositoryInterface $adminUserRepository,
        AdminUserInterface $adminUser,
    ): void {
        $security->getUser()->willReturn($adminUser);

        $tokenStorage->getToken()->shouldNotBeCalled();
        $requestStack->getMainRequest()->shouldNotBeCalled();
        $requestStack->getSession()->shouldNotBeCalled();
        $adminUserRepository->find(Argument::any())->shouldNotBeCalled();

        $this->getUser()->shouldReturn($adminUser);
    }

    function it_gets_user_from_token_storage(
        Security $security,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack,
        UserRepositoryInterface $adminUserRepository,
        TokenInterface $token,
        AdminUserInterface $adminUser,
    ): void {
        $security->getUser()->willReturn(null);

        $token->getUser()->willReturn($adminUser);
        $tokenStorage->getToken()->willReturn($token);

        $requestStack->getMainRequest()->shouldNotBeCalled();
        $requestStack->getSession()->shouldNotBeCalled();
        $adminUserRepository->find(Argument::any())->shouldNotBeCalled();

        $this->getUser()->shouldReturn($adminUser);
    }

    function it_gets_user_from_main_request_session(
        Security $security,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack,
        UserRepositoryInterface $adminUserRepository,
        Request $request,
        Session $session,
        TokenInterface $token,
        AdminUserInterface $adminUser,
    ): void {
        $security->getUser()->willReturn(null);
        $tokenStorage->getToken()->willReturn(null);

        $token->getUser()->willReturn($adminUser);
        $session->get(self::SECURITY_SESSION_KEY)->willReturn(self::SERIALIZED_TOKEN);
        $request->getSession()->willReturn($session);
        $requestStack->getMainRequest()->willReturn($request);

        $adminUser->getId()->willReturn(404);
        $adminUserRepository->find(404)->willReturn($adminUser);

        $requestStack->getSession()->shouldNotBeCalled();

        $this->getUser()->shouldReturn($adminUser);
    }

    function it_gets_user_from_current_request_session(
        Security $security,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack,
        UserRepositoryInterface $adminUserRepository,
        Request $request,
        Session $session,
        TokenInterface $token,
        AdminUserInterface $adminUser,
    ): void {
        $security->getUser()->willReturn(null);
        $tokenStorage->getToken()->willReturn(null);
        $requestStack->getMainRequest()->willReturn(null);

        $token->getUser()->willReturn($adminUser);
        $session->get(self::SECURITY_SESSION_KEY)->willReturn(self::SERIALIZED_TOKEN);
        $request->getSession()->willReturn($session);
        $requestStack->getSession()->willReturn($session);

        $adminUser->getId()->willReturn(404);
        $adminUserRepository->find(404)->willReturn($adminUser);

        $this->getUser()->shouldReturn($adminUser);
    }

    function it_returns_null_when_user_cannot_be_provided(
        Security $security,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack,
        UserRepositoryInterface $adminUserRepository,
    ): void {
        $security->getUser()->willReturn(null);
        $tokenStorage->getToken()->willReturn(null);
        $requestStack->getMainRequest()->willReturn(null);
        $requestStack->getSession()->willThrow(SessionNotFoundException::class);

        $adminUserRepository->find(Argument::any())->shouldNotBeCalled();

        $this->getUser()->shouldReturn(null);
    }
}
