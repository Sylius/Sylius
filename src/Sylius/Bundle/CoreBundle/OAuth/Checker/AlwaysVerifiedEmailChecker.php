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

final class AlwaysVerifiedEmailChecker implements EmailVerificationCheckerInterface
{
    public function isEmailVerified(UserResponseInterface $response): bool
    {
        return true;
    }
}
