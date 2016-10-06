<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Checker\Eligibility;

use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface PromotionEligibilityCheckerInterface
{
    /**
     * @param PromotionSubjectInterface $promotionSubject
     * @param PromotionInterface $promotion
     *
     * @return bool
     */
    public function isEligible(PromotionSubjectInterface $promotionSubject, PromotionInterface $promotion);
}
