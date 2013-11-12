<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Checker;

use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;

/**
 * Promotion eligibility checker interface.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface PromotionEligibilityCheckerInterface
{
    /**
     * @param PromotionSubjectInterface $subject
     * @param PromotionInterface        $promotion
     *
     * @return Boolean
     */
    public function isEligible(PromotionSubjectInterface $subject, PromotionInterface $promotion);
}
