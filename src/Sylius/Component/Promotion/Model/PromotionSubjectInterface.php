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
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface PromotionSubjectInterface
{
    /**
     * @return int
     */
    public function getPromotionSubjectTotal();

    /**
     * @return Collection|PromotionInterface[]
     */
    public function getPromotions();

    /**
     * @param PromotionInterface $promotion
     *
     * @return bool
     */
    public function hasPromotion(PromotionInterface $promotion);

    /**
     * @param PromotionInterface $promotion
     */
    public function addPromotion(PromotionInterface $promotion);

    /**
     * @param PromotionInterface $promotion
     */
    public function removePromotion(PromotionInterface $promotion);
}
