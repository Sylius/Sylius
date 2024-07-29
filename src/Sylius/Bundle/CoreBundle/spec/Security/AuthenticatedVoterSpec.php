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

namespace spec\Sylius\Bundle\CoreBundle\Security;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Security\AuthenticatedVoter;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter as VoterAuthenticatedVoter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class AuthenticatedVoterSpec extends ObjectBehavior
{
    private const FIREWALL_NAME = 'test';

    function let(VoterAuthenticatedVoter $authenticatedVoter, RequestStack $requestStack, FirewallMap $firewallMap): void
    {
        $this->beConstructedWith($authenticatedVoter, $requestStack, $firewallMap);
    }

    function it_supports_sylius_authenticated_attribute(): void
    {
        $this->supportsAttribute(AuthenticatedVoter::IS_IMPERSONATOR_SYLIUS)->shouldReturn(true);
    }

    function it_votes_granted_when_token_user_is_impersonated(
        TokenInterface $token,
        RequestStack $requestStack,
        SessionInterface $session,
    ): void {
        $requestStack->getSession()->willReturn($session);
        $session->get(sprintf('_security_impersonate_sylius_%s', self::FIREWALL_NAME), false)->willReturn(true);

        $this->vote($token, self::FIREWALL_NAME, [AuthenticatedVoter::IS_IMPERSONATOR_SYLIUS])->shouldReturn(VoterInterface::ACCESS_GRANTED);
    }

    function it_votes_denied_when_token_user_is_not_impersonated(
        TokenInterface $token,
        RequestStack $requestStack,
        SessionInterface $session,
    ): void {
        $requestStack->getSession()->willReturn($session);
        $session->get(sprintf('_security_impersonate_sylius_%s', self::FIREWALL_NAME), false)->willReturn(false);

        $this->vote($token, self::FIREWALL_NAME, [AuthenticatedVoter::IS_IMPERSONATOR_SYLIUS])->shouldReturn(VoterInterface::ACCESS_DENIED);
    }
}
