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

use Sylius\Component\Resource\Model\ActionInterface as BaseActionInterface;

/**
 * Promotion action model interface.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface ActionInterface extends BaseActionInterface
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
