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

use Sylius\Component\Core\Promotion\Action\FixedDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\PercentageDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\ShippingPercentageDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\UnitFixedDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\UnitPercentageDiscountPromotionActionCommand;
use Sylius\Component\Promotion\Model\PromotionActionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class PromotionActionFactory implements PromotionActionFactoryInterface
{
    /** @var FactoryInterface */
    private $decoratedFactory;

    public function __construct(FactoryInterface $decoratedFactory)
    {
        $this->decoratedFactory = $decoratedFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew(): PromotionActionInterface
    {
        return $this->decoratedFactory->createNew();
    }

    /**
     * {@inheritdoc}
     */
    public function createFixedDiscount(int $amount, string $channelCode): PromotionActionInterface
    {
        return $this->createAction(
            FixedDiscountPromotionActionCommand::TYPE,
            [$channelCode => ['amount' => $amount]]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createUnitFixedDiscount(int $amount, string $channelCode): PromotionActionInterface
    {
        return $this->createAction(
            UnitFixedDiscountPromotionActionCommand::TYPE,
            [$channelCode => ['amount' => $amount]]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createPercentageDiscount(float $percentage): PromotionActionInterface
    {
        return $this->createAction(
            PercentageDiscountPromotionActionCommand::TYPE,
            ['percentage' => $percentage]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createUnitPercentageDiscount(float $percentage, string $channelCode): PromotionActionInterface
    {
        return $this->createAction(
            UnitPercentageDiscountPromotionActionCommand::TYPE,
            [$channelCode => ['percentage' => $percentage]]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createShippingPercentageDiscount(float $percentage): PromotionActionInterface
    {
        return $this->createAction(
            ShippingPercentageDiscountPromotionActionCommand::TYPE,
            ['percentage' => $percentage]
        );
    }

    private function createAction(string $type, array $configuration): PromotionActionInterface
    {
        /** @var PromotionActionInterface $action */
        $action = $this->createNew();
        $action->setType($type);
        $action->setConfiguration($configuration);

        return $action;
    }
}
