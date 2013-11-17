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

use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;

/**
 * Applies promotion to given subject.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface PromotionApplicatorInterface
{
    public function apply(PromotionSubjectInterface $subject, PromotionInterface $promotion);
}
