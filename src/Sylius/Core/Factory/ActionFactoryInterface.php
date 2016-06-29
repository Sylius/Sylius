<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Core\Factory;

use Sylius\Promotion\Model\ActionInterface;
use Sylius\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface ActionFactoryInterface extends FactoryInterface
{
    /**
     * @param int $amount
     *
     * @return ActionInterface
     */
    public function createFixedDiscount($amount);

    /**
     * @param int $amount
     *
     * @return ActionInterface
     */
    public function createUnitFixedDiscount($amount);

    /**
     * @param float $percentage
     *
     * @return ActionInterface
     */
    public function createPercentageDiscount($percentage);

    /**
     * @param float $percentage
     *
     * @return ActionInterface
     */
    public function createUnitPercentageDiscount($percentage);

    /**
     * @param float $percentage
     *
     * @return ActionInterface
     */
    public function createPercentageShippingDiscount($percentage);
}
