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

namespace Sylius\Component\Promotion\Action;

use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

interface PromotionApplicatorInterface
{
    /**
     * @param PromotionSubjectInterface $subject
     * @param PromotionInterface $promotion
     */
    public function apply(PromotionSubjectInterface $subject, PromotionInterface $promotion): void;

    /**
     * @param PromotionSubjectInterface $subject
     * @param PromotionInterface $promotion
     */
    public function revert(PromotionSubjectInterface $subject, PromotionInterface $promotion): void;
}
