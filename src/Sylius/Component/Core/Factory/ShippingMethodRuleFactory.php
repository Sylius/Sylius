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

use Sylius\Component\Core\Shipping\Checker\Rule\OrderTotalGreaterThanOrEqualRuleChecker;
use Sylius\Component\Core\Shipping\Checker\Rule\OrderTotalLessThanOrEqualRuleChecker;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Shipping\Checker\Rule\TotalWeightGreaterThanOrEqualRuleChecker;
use Sylius\Component\Shipping\Checker\Rule\TotalWeightLessThanOrEqualRuleChecker;
use Sylius\Component\Shipping\Model\ShippingMethodRuleInterface;

final class ShippingMethodRuleFactory implements ShippingMethodRuleFactoryInterface
{
    /** @var FactoryInterface */
    private $decoratedFactory;

    public function __construct(FactoryInterface $decoratedFactory)
    {
        $this->decoratedFactory = $decoratedFactory;
    }

    public function createNew(): ShippingMethodRuleInterface
    {
        return $this->decoratedFactory->createNew();
    }

    public function createOrderTotalGreaterThanOrEqual(string $channelCode, int $amount): ShippingMethodRuleInterface
    {
        return $this->createPromotionRule(OrderTotalGreaterThanOrEqualRuleChecker::TYPE, [
            $channelCode => ['amount' => $amount],
        ]);
    }

    public function createOrderTotalLessThanOrEqual(string $channelCode, int $amount): ShippingMethodRuleInterface
    {
        return $this->createPromotionRule(OrderTotalLessThanOrEqualRuleChecker::TYPE, [
            $channelCode => ['amount' => $amount],
        ]);
    }

    public function createWeightGreaterThanOrEqual(int $weight): ShippingMethodRuleInterface
    {
        return $this->createPromotionRule(TotalWeightGreaterThanOrEqualRuleChecker::TYPE, ['weight' => $weight]);
    }

    public function createWeightLessThanOrEqual(int $weight): ShippingMethodRuleInterface
    {
        return $this->createPromotionRule(TotalWeightLessThanOrEqualRuleChecker::TYPE, ['weight' => $weight]);
    }

    private function createPromotionRule(string $type, array $configuration): ShippingMethodRuleInterface
    {
        $rule = $this->createNew();
        $rule->setType($type);
        $rule->setConfiguration($configuration);

        return $rule;
    }
}
