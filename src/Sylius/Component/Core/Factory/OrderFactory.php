<?php

namespace Sylius\Component\Core\Factory;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\TokenAssigner\OrderTokenAssignerInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @implements FactoryInterface<OrderInterface>
 */
class OrderFactory implements FactoryInterface
{
    public function __construct(
        private FactoryInterface $decoratedFactory,
        private OrderTokenAssignerInterface $tokenAssigner
    ) {
    }

    public function createNew(): OrderInterface
    {
        $order = $this->decoratedFactory->createNew();

        $this->tokenAssigner->assignTokenValue($order);

        return $order;
    }
}
