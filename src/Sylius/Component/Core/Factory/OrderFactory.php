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
    private FactoryInterface $decoratedFactory;
    private OrderTokenAssignerInterface $tokenAssigner;

    public function __construct(FactoryInterface $decoratedFactory, OrderTokenAssignerInterface $tokenAssigner)
    {
        $this->decoratedFactory = $decoratedFactory;
        $this->tokenAssigner = $tokenAssigner;
    }

    public function createNew()
    {
        $order = $this->decoratedFactory->createNew();

        $this->tokenAssigner->assignTokenValue($order);

        return $order;
    }
}
