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
final class ShopUserNotVerified extends Constraint
{
    /** @var string */
    public $message = 'sylius.account.is_verified';

    public function validatedBy(): string
    {
        return 'sylius_api_shop_user_not_verified';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
