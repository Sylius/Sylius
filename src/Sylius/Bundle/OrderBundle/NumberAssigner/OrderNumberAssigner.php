<?php

/*
 * This file is a part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\NumberAssigner;

use Sylius\Bundle\OrderBundle\NumberGenerator\SequentialOrderNumberGeneratorInterface;
use Sylius\Component\Order\Model\OrderInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class OrderNumberAssigner implements OrderNumberAssignerInterface
{
    /**
     * @var SequentialOrderNumberGeneratorInterface
     */
    private $numberGenerator;

    /**
     * @param SequentialOrderNumberGeneratorInterface $numberGenerator
     */
    public function __construct(SequentialOrderNumberGeneratorInterface $numberGenerator)
    {
        $this->numberGenerator = $numberGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function assignNumber(OrderInterface $order)
    {
        if (null !== $order->getNumber()) {
            return;
        }

        $order->setNumber($this->numberGenerator->generate());
    }
}
