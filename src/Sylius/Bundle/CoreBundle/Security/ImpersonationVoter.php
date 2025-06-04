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

use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\CacheableVoterInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class ImpersonationVoter implements CacheableVoterInterface
{
    public const IS_IMPERSONATOR_SYLIUS = 'IS_IMPERSONATOR_SYLIUS';

    public function __construct(
        private RequestStack $requestStack,
        private FirewallMap $firewallMap,
    ) {
    }

    public function vote(TokenInterface $token, mixed $subject, array $attributes): int
    {
        $result = VoterInterface::ACCESS_ABSTAIN;

        foreach ($attributes as $attribute) {
            if (self::IS_IMPERSONATOR_SYLIUS !== $attribute) {
                continue;
            }

            $firewall = $subject ?? $this->getMainRequestFirewallName();

            if ($firewall) {
                try {
                    return $this->isImpersonated($firewall)
                        ? VoterInterface::ACCESS_GRANTED
                        : VoterInterface::ACCESS_DENIED;
                } catch (SessionNotFoundException) {
                }
            }

            break;
        }

        return $result;
    }

    public function supportsAttribute(string $attribute): bool
    {
        return self::IS_IMPERSONATOR_SYLIUS === $attribute;
    }

    public function supportsType(string $subjectType): bool
    {
        return true;
    }

    /**
     * @throws SessionNotFoundException
     */
    private function isImpersonated(string $firewall): bool
    {
        return (bool) $this->requestStack->getSession()->get(
            $this->getImpersonateKeyName($firewall),
            false,
        );
    }

    private function getMainRequestFirewallName(): ?string
    {
        $request = $this->requestStack->getMainRequest();

        if (!$request) {
            return null;
        }

        return $this->firewallMap->getFirewallConfig($request)?->getName();
    }

    private function getImpersonateKeyName(string $firewall): string
    {
        return sprintf('_security_impersonate_sylius_%s', $firewall);
    }
}
