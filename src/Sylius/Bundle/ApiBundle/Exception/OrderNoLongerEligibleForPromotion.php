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

final class OrderNoLongerEligibleForPromotion extends \RuntimeException
{
    public function __construct(string $promotionName)
    {
        parent::__construct(\sprintf('Order is no longer eligible for this %s promotion. Your cart was recalculated.', $promotionName));
    }
}
