<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle\Model;

/**
 * Promotion action model interface.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface ActionInterface
{
    const TYPE_FIXED_DISCOUNT = 'fixed_discount';

    public function getId();
    public function getType();
    public function setType($type);
    public function getConfiguration();
    public function setConfiguration(array $configuration);
    public function getPromotion();
    public function setPromotion(PromotionInterface $promotion = null);
}
