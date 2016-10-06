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

use Sylius\Component\Core\Promotion\Action\FixedDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\ShippingPercentageDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\UnitFixedDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\UnitPercentageDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\PercentageDiscountPromotionActionCommand;
use Sylius\Component\Promotion\Model\ActionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ActionFactory implements ActionFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $decoratedFactory;

    /**
     * @param FactoryInterface $decoratedFactory
     */
    public function __construct(FactoryInterface $decoratedFactory)
    {
        $this->decoratedFactory = $decoratedFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        return $this->decoratedFactory->createNew();
    }

    /**
     * {@inheritdoc}
     */
    public function createFixedDiscount($amount)
    {
        return $this->createAction(FixedDiscountPromotionActionCommand::TYPE, ['amount' => $amount]);
    }

    /**
     * {@inheritdoc}
     */
    public function createUnitFixedDiscount($amount)
    {
        return $this->createAction(UnitFixedDiscountPromotionActionCommand::TYPE, ['amount' => $amount]);
    }

    /**
     * {@inheritdoc}
     */
    public function createPercentageDiscount($percentage)
    {
        return $this->createAction(PercentageDiscountPromotionActionCommand::TYPE, ['percentage' => $percentage]);
    }

    /**
     * {@inheritdoc}
     */
    public function createUnitPercentageDiscount($percentage)
    {
        return $this->createAction(UnitPercentageDiscountPromotionActionCommand::TYPE, ['percentage' => $percentage]);
    }

    /**
     * {@inheritdoc}
     */
    public function createShippingPercentageDiscount($percentage)
    {
        return $this->createAction(ShippingPercentageDiscountPromotionActionCommand::TYPE, ['percentage' => $percentage]);
    }

    /**
     * @param string $type
     * @param array $configuration
     *
     * @return ActionInterface
     */
    private function createAction($type, array $configuration)
    {
        $action = $this->createNew();
        $action->setType($type);
        $action->setConfiguration($configuration);

        return $action;
    }
}
