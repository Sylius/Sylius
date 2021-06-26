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

use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Shipping\Model\ShippingMethodRuleInterface;

interface ShippingMethodRuleFactoryInterface extends FactoryInterface
{
    public function createOrderTotalGreaterThanOrEqual(string $channelCode, int $amount): ShippingMethodRuleInterface;

    public function createOrderTotalLessThanOrEqual(string $channelCode, int $amount): ShippingMethodRuleInterface;

    public function createWeightGreaterThanOrEqual(int $amount): ShippingMethodRuleInterface;

    public function createWeightLessThanOrEqual(int $amount): ShippingMethodRuleInterface;
}
