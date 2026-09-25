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

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;

final readonly class TrustedResourceOwnersEmailVerificationChecker implements EmailVerificationCheckerInterface
{
    /** @param list<string> $trustedResourceOwners */
    public function __construct(
        private EmailVerificationCheckerInterface $emailVerificationChecker,
        private array $trustedResourceOwners = [],
    ) {
    }

    public function isEmailVerified(UserResponseInterface $response): bool
    {
        if (\in_array($response->getResourceOwner()->getName(), $this->trustedResourceOwners, true)) {
            return true;
        }

        return $this->emailVerificationChecker->isEmailVerified($response);
    }
}
