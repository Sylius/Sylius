<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

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
    public function createFixedDiscount(int $amount, string $channelCode): PromotionActionInterface;

    /**
     * @param int $amount
     * @param string $channelCode
     *
     * @return PromotionActionInterface
     */
    public function createUnitFixedDiscount(int $amount, string $channelCode): PromotionActionInterface;

    /**
     * @param float $percentage
     *
     * @return PromotionActionInterface
     */
    public function createPercentageDiscount(float $percentage): PromotionActionInterface;

    /**
     * @param float $percentage
     * @param string $channelCode
     *
     * @return PromotionActionInterface
     */
    public function createUnitPercentageDiscount(float $percentage, string $channelCode): PromotionActionInterface;

    /**
     * @param float $percentage
     *
     * @return PromotionActionInterface
     */
    public function createShippingPercentageDiscount(float $percentage): PromotionActionInterface;
}
