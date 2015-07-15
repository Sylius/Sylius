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

use Doctrine\Common\Collections\Collection;

/**
 * Promotion subject interface.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface PromotionSubjectInterface
{
    /**
     * Get total of promotion subject.
     *
     * @return int
     */
    public function getPromotionSubjectTotal();

    /**
     * Has Promotion.
     *
     * @param PromotionInterface $promotion
     *
     * @return Boolean
     */
    public function hasPromotion(PromotionInterface $promotion);

    /**
     * Add Promotion.
     *
     * @param PromotionInterface $promotion
     *
     * @return self
     */
    public function addPromotion(PromotionInterface $promotion);

    /**
     * Remove Promotion.
     *
     * @param PromotionInterface $promotion
     *
     * @return self
     */
    public function removePromotion(PromotionInterface $promotion);

    /**
     * Get Promotions.
     *
     * @return Collection|PromotionInterface[]
     */
    public function getPromotions();
}
