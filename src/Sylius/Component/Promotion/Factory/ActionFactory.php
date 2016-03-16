<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Factory;

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
        $action = $this->createNew();
        $action->setType(ActionInterface::TYPE_FIXED_DISCOUNT);
        $action->setConfiguration(['amount' => $amount]);

        return $action;
    }

    /**
     * {@inheritdoc}
     */
    public function createPercentageDiscount($percentage)
    {
        $action = $this->createNew();
        $action->setType(ActionInterface::TYPE_PERCENTAGE_DISCOUNT);
        $action->setConfiguration(['percentage' => $percentage]);

        return $action;
    }

    /**
     * {@inheritdoc}
     */
    public function createPercentageShippingDiscount($percentage)
    {
        $action = $this->createNew();
        $action->setType('shipping_discount');
        $action->setConfiguration(['percentage' => $percentage]);

        return $action;
    }
}
