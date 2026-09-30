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

namespace Sylius\Bundle\CoreBundle\OAuth\Checker;

use HWI\Bundle\OAuthBundle\OAuth\ResourceOwnerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\PathUserResponse;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
final readonly class ResponseDataEmailVerificationChecker implements EmailVerificationCheckerInterface
{
    public const PATH_NAME = 'email_verified';

    /** @var list<string> */
    public const CLAIMS = ['email_verified', 'verified_email'];

    public function __construct(private LoggerInterface $logger = new NullLogger())
    {
    }

    public function isEmailVerified(UserResponseInterface $response): bool
    {
        $data = $response->getData();

        if (!\is_array($data)) {
            $this->logger->warning('The response of the OAuth resource owner "{resource_owner}" carries no array of data, so the e-mail address is taken as unverified.', [
                'resource_owner' => $this->getResourceOwnerName($response),
                'data_type' => get_debug_type($data),
            ]);

            return false;
        }

        $claim = $this->findClaim($response, $data);

        if (null === $claim) {
            $this->logger->info('The OAuth resource owner "{resource_owner}" returns no e-mail verification claim at all, so the e-mail address is taken as unverified. Add it to "sylius_core.oauth.account_linking.trusted_resource_owners" to link its accounts regardless.', [
                'resource_owner' => $this->getResourceOwnerName($response),
                'claims' => self::CLAIMS,
            ]);

            return false;
        }

        [$name, $value] = $claim;

        if (true !== filter_var($value, \FILTER_VALIDATE_BOOLEAN, \FILTER_NULL_ON_FAILURE)) {
            $this->logger->info('The OAuth resource owner "{resource_owner}" reports the e-mail address as unverified through its "{claim}" claim.', [
                'resource_owner' => $this->getResourceOwnerName($response),
                'claim' => $name,
                'value' => $value,
            ]);

            return false;
        }

        return true;
    }

    /**
     * @param array<array-key, mixed> $data
     *
     * @return array{string, mixed}|null the claim name and its value, or null when the response carries no claim
     */
    private function findClaim(UserResponseInterface $response, array $data): ?array
    {
        if ($response instanceof PathUserResponse && null !== $path = $response->getPath(self::PATH_NAME)) {
            return [\is_string($path) ? $path : self::PATH_NAME, $this->getValueForPath($path, $data)];
        }

        foreach (self::CLAIMS as $claim) {
            if (\array_key_exists($claim, $data)) {
                return [$claim, $data[$claim]];
            }
        }

        return null;
    }

    /**
     * @param array<array-key, mixed> $data
     */
    private function getValueForPath(mixed $path, array $data): mixed
    {
        if (!\is_string($path)) {
            return null;
        }

        $value = $data;
        foreach (explode('.', $path) as $step) {
            if (!\is_array($value) || !\array_key_exists($step, $value)) {
                return null;
            }

            $value = $value[$step];
        }

        return $value;
    }

    private function getResourceOwnerName(UserResponseInterface $response): string
    {
        $resourceOwner = $response->getResourceOwner();

        return $resourceOwner instanceof ResourceOwnerInterface ? $resourceOwner->getName() : 'unknown';
    }
}
