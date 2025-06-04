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

namespace Tests\Sylius\Bundle\CoreBundle\Security;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Security\ImpersonationVoter;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class ImpersonationVoterTest extends TestCase
{
    private const FIREWALL_NAME = 'test';

    private MockObject&RequestStack $requestStack;

    private FirewallMap&MockObject $firewallMap;

    private ImpersonationVoter $voter;

    protected function setUp(): void
    {
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->firewallMap = $this->createMock(FirewallMap::class);

        $this->voter = new ImpersonationVoter($this->requestStack, $this->firewallMap);
    }

    public function testSupportsSyliusAuthenticatedAttribute(): void
    {
        $this->assertTrue($this->voter->supportsAttribute(ImpersonationVoter::IS_IMPERSONATOR_SYLIUS));
    }

    public function testVotesGrantedWhenTokenUserIsImpersonated(): void
    {
        $session = $this->createMock(SessionInterface::class);
        $token = $this->createMock(TokenInterface::class);

        $this->requestStack->method('getSession')->willReturn($session);

        $session->method('get')
            ->with(sprintf('_security_impersonate_sylius_%s', self::FIREWALL_NAME), false)
            ->willReturn(true)
        ;

        $result = $this->voter->vote($token, self::FIREWALL_NAME, [ImpersonationVoter::IS_IMPERSONATOR_SYLIUS]);
        $this->assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testVotesDeniedWhenTokenUserIsNotImpersonated(): void
    {
        $session = $this->createMock(SessionInterface::class);
        $token = $this->createMock(TokenInterface::class);

        $this->requestStack->method('getSession')->willReturn($session);

        $session->method('get')
            ->with(sprintf('_security_impersonate_sylius_%s', self::FIREWALL_NAME), false)
            ->willReturn(false)
        ;

        $result = $this->voter->vote($token, self::FIREWALL_NAME, [ImpersonationVoter::IS_IMPERSONATOR_SYLIUS]);
        $this->assertSame(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testVotesAbstainWhenSessionDoesNotExists(): void
    {
        $token = $this->createMock(TokenInterface::class);

        $this->requestStack->method('getSession')->will($this->throwException(new SessionNotFoundException()));

        $result = $this->voter->vote($token, self::FIREWALL_NAME, [ImpersonationVoter::IS_IMPERSONATOR_SYLIUS]);
        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    public function testVotesAbstainWhenFirewallIsNotSet(): void
    {
        $request = $this->createMock(Request::class);
        $token = $this->createMock(TokenInterface::class);

        $this->requestStack->method('getMainRequest')->willReturn($request);
        $this->firewallMap->method('getFirewallConfig')->with($request)->willReturn(null);

        $result = $this->voter->vote($token, null, [ImpersonationVoter::IS_IMPERSONATOR_SYLIUS]);
        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $result);
    }
}
