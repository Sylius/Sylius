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

namespace Sylius\Component\Core\Promotion\Exception;

use Doctrine\ORM\OptimisticLockException;

final class PromotionUsageLimitReachedException extends OptimisticLockException
{
    private function __construct(string $message)
    {
        parent::__construct($message, null);
    }

    public static function withPromotionCode(string $code): self
    {
        return new self(sprintf('The "%s" promotion is no longer applicable.', $code));
    }

    public static function withCouponCode(string $code): self
    {
        return new self(sprintf('The "%s" promotion coupon is no longer applicable.', $code));
    }
}
