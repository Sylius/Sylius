<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Core\Promotion\Applicator;

use Sylius\Core\Model\OrderInterface;
use Sylius\Promotion\Model\PromotionInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface UnitsPromotionAdjustmentsApplicatorInterface
{
    /**
     * @param OrderInterface $order
     * @param PromotionInterface $promotion
     * @param array $adjustmentsAmounts
     */
    public function apply(OrderInterface $order, PromotionInterface $promotion, array $adjustmentsAmounts);
}
