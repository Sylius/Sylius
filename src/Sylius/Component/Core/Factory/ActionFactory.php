<?php

/*
 * This file is a part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Factory;

use Sylius\Component\Core\Promotion\Action\FixedDiscountAction;
use Sylius\Component\Core\Promotion\Action\UnitFixedDiscountAction;
use Sylius\Component\Core\Promotion\Action\UnitPercentageDiscountAction;
use Sylius\Component\Core\Promotion\Action\PercentageDiscountAction;
use Sylius\Component\Core\Promotion\Action\ShippingDiscountAction;
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
        return $this->createAction(FixedDiscountAction::TYPE, ['amount' => $amount]);
    }

    /**
     * {@inheritdoc}
     */
    public function createUnitFixedDiscount($amount)
    {
        return $this->createAction(UnitFixedDiscountAction::TYPE, ['amount' => $amount]);
    }

    /**
     * {@inheritdoc}
     */
    public function createPercentageDiscount($percentage)
    {
        return $this->createAction(PercentageDiscountAction::TYPE, ['percentage' => $percentage]);
    }

    /**
     * {@inheritdoc}
     */
    public function createUnitPercentageDiscount($percentage)
    {
        return $this->createAction(UnitPercentageDiscountAction::TYPE, ['percentage' => $percentage]);
    }

    /**
     * {@inheritdoc}
     */
    public function createPercentageShippingDiscount($percentage)
    {
        return $this->createAction(ShippingDiscountAction::TYPE, ['percentage' => $percentage]);
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
