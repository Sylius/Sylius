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

namespace Sylius\Component\Promotion\Generator;

use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;

interface PromotionCouponGeneratorInterface
{
    /**
     * @param PromotionInterface $promotion
     * @param PromotionCouponGeneratorInstructionInterface $instruction
     *
     * @return array|PromotionCouponInterface[]
     */
    public function generate(PromotionInterface $promotion, PromotionCouponGeneratorInstructionInterface $instruction): array;
}
