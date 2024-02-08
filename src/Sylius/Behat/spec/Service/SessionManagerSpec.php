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

namespace spec\Sylius\Behat\Service;

use Behat\Mink\Mink;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Behat\Service\SecurityServiceInterface;
use Sylius\Behat\Service\SessionManagerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class SessionManagerSpec extends ObjectBehavior
{
    function let(Mink $mink, SharedStorageInterface $sharedStorage, SecurityServiceInterface $securityService)
    {
        $this->beConstructedWith($mink, $sharedStorage, $securityService);
    }

    function it_implements_session_service_interface(): void
    {
        $this->shouldImplement(SessionManagerInterface::class);
    }

    function it_changes_session_and_does_not_restore_session_token_if_session_was_not_called_before(
        Mink $mink,
        SharedStorageInterface $sharedStorage,
        SecurityServiceInterface $securityService,
        TokenInterface $token,
    ): void {
        $mink->getDefaultSessionName()->willReturn('default_session');
        $securityService->getCurrentToken()->willReturn($token);

        $token->__toString()->willReturn('{JSON_TOKEN}');

        $sharedStorage->set('behat_previous_session_name', 'default_session')->shouldBeCalled();
        $sharedStorage->set('behat_previous_session_token_default_session', $token)->shouldBeCalled();

        $mink->setDefaultSessionName('chrome_headless_second_session')->shouldBeCalled();
        $mink->restartSessions()->shouldBeCalled();

        $sharedStorage->has('behat_previous_session_token_chrome_headless_second_session')->willReturn(false);

        $securityService->restoreToken(Argument::any())->shouldNotBeCalled();

        $this->changeSession();
    }

    function it_changes_session_and_restores_session_token_if_session_was_called_before(
        Mink $mink,
        SharedStorageInterface $sharedStorage,
        SecurityServiceInterface $securityService,
        TokenInterface $token,
        TokenInterface $previousToken,
    ): void {
        $mink->getDefaultSessionName()->willReturn('default_session');
        $securityService->getCurrentToken()->willReturn($token);

        $token->__toString()->willReturn('{JSON_TOKEN}');

        $sharedStorage->set('behat_previous_session_name', 'default_session')->shouldBeCalled();
        $sharedStorage->set('behat_previous_session_token_default_session', $token)->shouldBeCalled();

        $mink->setDefaultSessionName('chrome_headless_second_session')->shouldBeCalled();
        $mink->restartSessions()->shouldBeCalled();

        $sharedStorage->has('behat_previous_session_token_chrome_headless_second_session')->willReturn(true);
        $sharedStorage->get('behat_previous_session_token_chrome_headless_second_session')->willReturn($previousToken);

        $securityService->restoreToken($previousToken)->shouldBeCalled();

        $this->changeSession();
    }

    function it_restores_session_and_token(
        Mink $mink,
        SharedStorageInterface $sharedStorage,
        SecurityServiceInterface $securityService,
        TokenInterface $token,
        TokenInterface $defaultToken,
    ): void {
        $sharedStorage->has('behat_previous_session_name')->willReturn(true);
        $sharedStorage->get('behat_previous_session_name')->willReturn('previous_session');

        $mink->getDefaultSessionName()->willReturn('default_session');
        $defaultToken->__toString()->willReturn('{JSON_DEFAULT_TOKEN}');

        $sharedStorage->set('behat_previous_session_name', 'default_session')->shouldBeCalled();
        $sharedStorage->set('behat_previous_session_token_default_session', $defaultToken)->shouldBeCalled();

        $mink->setDefaultSessionName('previous_session')->shouldBeCalled();
        $mink->restartSessions()->shouldBeCalled();

        $securityService->getCurrentToken()->willReturn($defaultToken);
        $sharedStorage->has('behat_previous_session_token_previous_session')->willReturn(true);
        $sharedStorage->get('behat_previous_session_token_previous_session')->willReturn($token);
        $securityService->restoreToken($token)->shouldBeCalled();

        $this->restorePreviousSession();
    }

    function it_does_not_restore_session_and_token_if_previous_session_was_never_called(
        Mink $mink,
        SharedStorageInterface $sharedStorage,
        SecurityServiceInterface $securityService,
        TokenInterface $token,
    ): void {
        $sharedStorage->has('behat_previous_session_name')->willReturn(false);

        $mink->getDefaultSessionName()->shouldNotBeCalled();
        $sharedStorage->set('behat_previous_session_name', 'default_session')->shouldNotBeCalled();

        $mink->setDefaultSessionName('previous_session')->shouldNotBeCalled();
        $mink->restartSessions()->shouldNotBeCalled();

        $securityService->restoreToken($token)->shouldNotBeCalled();

        $this->restorePreviousSession();
    }
}
