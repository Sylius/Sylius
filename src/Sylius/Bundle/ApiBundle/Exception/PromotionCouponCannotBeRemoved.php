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

namespace Sylius\Bundle\ApiBundle\Exception;

trigger_deprecation(
    'sylius/api-bundle',
    '1.14',
    'The "%s" class is deprecated. Will be removed in Sylius 2.0.',
    PromotionCouponCannotBeRemoved::class,
);
/** @deprecated since Sylius 1.14 and will be removed in Sylius 2.0. */
final class PromotionCouponCannotBeRemoved extends \RuntimeException
{
    public function __construct(
        string $message = 'Cannot delete, the promotion coupon is in use.',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
