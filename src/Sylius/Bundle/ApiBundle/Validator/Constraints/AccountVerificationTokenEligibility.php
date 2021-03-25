<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/** @experimental */
final class AccountVerificationTokenEligibility extends Constraint
{
    /** @var string */
    public $message = 'sylius.account.invalid_verification_token';

    public function validatedBy(): string
    {
        return 'sylius_api_validator_account_verification_token_eligibility';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
