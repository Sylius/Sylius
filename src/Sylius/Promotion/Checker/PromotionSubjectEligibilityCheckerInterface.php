<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Promotion\Checker;

use Sylius\Promotion\Model\PromotionInterface;
use Sylius\Promotion\Model\PromotionSubjectInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface PromotionSubjectEligibilityCheckerInterface
{
    /**
     * @param PromotionSubjectInterface $subject
     * @param PromotionInterface $promotion
     *
     * @return bool
     */
    public function isEligible(PromotionSubjectInterface $subject, PromotionInterface $promotion);
}
