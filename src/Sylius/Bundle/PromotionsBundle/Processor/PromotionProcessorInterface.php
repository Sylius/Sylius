<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle\Processor;

use Sylius\Bundle\SalesBundle\Model\OrderInterface;
use Sylius\Bundle\PromotionsBundle\Model\PromotionInterface;

/**
 * Promotion processor interface.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface PromotionProcessorInterface
{
    public function process(OrderInterface $order, PromotionInterface $promotion);
}
