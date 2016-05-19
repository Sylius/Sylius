<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Order\Factory;

use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class AdjustmentFactory implements AdjustmentFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $adjustmentFactory;

    /**
     * @param FactoryInterface $adjustmentFactory
     */
    public function __construct(FactoryInterface $adjustmentFactory)
    {
        $this->adjustmentFactory = $adjustmentFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        return $this->adjustmentFactory->createNew();
    }

    /**
     * {@inheritdoc}
     */
    public function createWithData($type, $label, $amount, $neutral = false)
    {
        $adjustment = $this->createNew();
        $adjustment->setType($type);
        $adjustment->setLabel($label);
        $adjustment->setAmount($amount);
        $adjustment->setNeutral($neutral);

        return $adjustment;
    }
}
