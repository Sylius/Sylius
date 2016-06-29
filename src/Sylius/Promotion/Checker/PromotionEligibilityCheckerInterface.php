<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Promotion\Checker;

use Sylius\Promotion\Model\PromotionInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface PromotionEligibilityCheckerInterface
{
    /**
     * @param PromotionInterface $promotion
     *
     * @return bool
     */
    public function isEligible(PromotionInterface $promotion);
}
