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

use Sylius\Component\Core\Promotion\Action\FixedDiscountAction;
use Sylius\Component\Core\Promotion\Action\PercentageDiscountAction;
use Sylius\Component\Core\Promotion\Action\ShippingDiscountAction;
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
        $action = $this->createNew();
        $action->setType(FixedDiscountAction::TYPE);
        $action->setConfiguration(['amount' => $amount]);

        return $action;
    }

    /**
     * {@inheritdoc}
     */
    public function createPercentageDiscount($percentage)
    {
        $action = $this->createNew();
        $action->setType(PercentageDiscountAction::TYPE);
        $action->setConfiguration(['percentage' => $percentage]);

        return $action;
    }

    /**
     * {@inheritdoc}
     */
    public function createPercentageShippingDiscount($percentage)
    {
        $action = $this->createNew();
        $action->setType(ShippingDiscountAction::TYPE);
        $action->setConfiguration(['percentage' => $percentage]);

        return $action;
    }
}
