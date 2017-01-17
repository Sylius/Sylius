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
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface PromotionApplicatorInterface
{
    /**
     * @param PromotionSubjectInterface $subject
     * @param PromotionInterface $promotion
     */
    public function apply(PromotionSubjectInterface $subject, PromotionInterface $promotion);

    /**
     * @param PromotionSubjectInterface $subject
     * @param PromotionInterface $promotion
     */
    public function revert(PromotionSubjectInterface $subject, PromotionInterface $promotion);
}
