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

namespace Sylius\Component\Promotion\Generator;

use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;

interface PromotionCouponGeneratorInterface
{
    /**
     * @return array|PromotionCouponInterface[]
     */
    public function generate(PromotionInterface $promotion, ReadablePromotionCouponGeneratorInstructionInterface $instruction): array;
}
