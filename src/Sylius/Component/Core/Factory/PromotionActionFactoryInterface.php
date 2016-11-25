<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Factory;

use Sylius\Component\Promotion\Model\PromotionActionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface PromotionActionFactoryInterface extends FactoryInterface
{
    /**
     * @param int $amount
     * @param string $channelCode
     *
     * @return PromotionActionInterface
     */
    public function createFixedDiscount($amount, $channelCode);

    /**
     * @param int $amount
     * @param string $channelCode
     *
     * @return PromotionActionInterface
     */
    public function createUnitFixedDiscount($amount, $channelCode);

    /**
     * @param float $percentage
     *
     * @return PromotionActionInterface
     */
    public function createPercentageDiscount($percentage);

    /**
     * @param float $percentage
     *
     * @return PromotionActionInterface
     */
    public function createUnitPercentageDiscount($percentage);

    /**
     * @param float $percentage
     *
     * @return PromotionActionInterface
     */
    public function createShippingPercentageDiscount($percentage);
}
