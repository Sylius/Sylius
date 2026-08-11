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

namespace Sylius\Bundle\ApiBundle\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTDecodedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\RequestStack;

/** @internal */
final class JwtAudienceListener
{
    /** @param array<string, array{audience: string, principal: class-string}> $firewallExpectations */
    public function __construct(
        private readonly FirewallMap $firewallMap,
        private readonly RequestStack $requestStack,
        private readonly LoggerInterface $logger,
        private readonly array $firewallExpectations,
    ) {
    }

    public function onJwtCreated(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();

        foreach ($this->firewallExpectations as $expectation) {
            if (!$user instanceof $expectation['principal']) {
                continue;
            }

            $event->setData([
                ...$event->getData(),
                'aud' => $expectation['audience'],
                'principal_type' => $expectation['principal'],
            ]);

            return;
        }
    }

    public function onJwtDecoded(JWTDecodedEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $firewallName = null !== $request ? $this->firewallMap->getFirewallConfig($request)?->getName() : null;

        $expectations = $this->firewallExpectations[$firewallName] ?? null;
        $payload = $event->getPayload();

        $audience = (array) ($payload['aud'] ?? []);

        if (
            null === $expectations ||
            !in_array($expectations['audience'], $audience, true) ||
            ($payload['principal_type'] ?? null) !== $expectations['principal']
        ) {
            $this->logger->warning('Rejected JWT due to audience/principal-type mismatch.', [
                'firewall' => $firewallName,
                'aud' => $payload['aud'] ?? null,
                'principal_type' => $payload['principal_type'] ?? null,
            ]);

            $event->markAsInvalid();
        }
    }
}
