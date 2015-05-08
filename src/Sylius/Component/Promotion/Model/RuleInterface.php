<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Model;

use Sylius\Component\Resource\Model\RuleInterface as BaseRuleInterface;

/**
 * Promotion rule model interface.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface RuleInterface extends BaseRuleInterface
{
    /**
     * Get associated promotion.
     *
     * @return PromotionInterface
     */
    public function getPromotion();

    /**
     * Set associated promotion.
     *
     * @param PromotionInterface $promotion
     */
    public function setPromotion(PromotionInterface $promotion = null);
}
