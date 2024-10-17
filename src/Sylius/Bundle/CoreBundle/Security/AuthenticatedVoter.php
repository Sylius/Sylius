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

namespace Sylius\Bundle\CoreBundle\Security;

use Sylius\Bundle\CoreBundle\Provider\SessionProvider;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\CacheableVoterInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class AuthenticatedVoter implements CacheableVoterInterface
{
    public const IS_IMPERSONATOR_SYLIUS = 'IS_IMPERSONATOR_SYLIUS';

    public function __construct(
        private CacheableVoterInterface $authenticatedVoter,
        private RequestStack $requestStack,
        private FirewallMap $firewallMap,
    ) {
    }

    public function vote(TokenInterface $token, mixed $subject, array $attributes): int
    {
        $result = VoterInterface::ACCESS_ABSTAIN;

        foreach ($attributes as $attribute) {
            if (null === $attribute || (self::IS_IMPERSONATOR_SYLIUS !== $attribute)) {
                continue;
            }

            $result = VoterInterface::ACCESS_DENIED;

            if (self::IS_IMPERSONATOR_SYLIUS === $attribute && $this->isImpersonated($subject)) {
                return VoterInterface::ACCESS_GRANTED;
            }
        }

        if (VoterInterface::ACCESS_ABSTAIN !== $result) {
            return $result;
        }

        return $this->authenticatedVoter->vote($token, $subject, $attributes);
    }

    public function supportsAttribute(string $attribute): bool
    {
        return \in_array($attribute, [
            self::IS_IMPERSONATOR_SYLIUS,
        ], true) || $this->authenticatedVoter->supportsAttribute($attribute);
    }

    public function supportsType(string $subjectType): bool
    {
        return $this->authenticatedVoter->supportsType($subjectType);
    }

    private function isImpersonated(?string $firewall = null): bool
    {
        if (!$firewall && $request = $this->requestStack->getMainRequest()) {
            $firewall = $this->firewallMap->getFirewallConfig($request)?->getName();
        }

        if (!$firewall) {
            return false;
        }

        $session = SessionProvider::getSession($this->requestStack);

        return (bool) $session->get(sprintf('_security_impersonate_sylius_%s', $firewall), false);
    }
}
