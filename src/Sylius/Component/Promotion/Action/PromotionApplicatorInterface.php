<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Action;

use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * Applies promotion to given subject.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface PromotionApplicatorInterface
{
    /**
     * Apply all promotion actions to the given subject
     *
     * @param PromotionSubjectInterface $subject
     * @param PromotionInterface        $promotion
     */
    public function apply(PromotionSubjectInterface $subject, PromotionInterface $promotion);

    /**
     * Revert all promotion actions to the given subject
     *
     * @param PromotionSubjectInterface $subject
     * @param PromotionInterface        $promotion
     */
    public function revert(PromotionSubjectInterface $subject, PromotionInterface $promotion);
}
