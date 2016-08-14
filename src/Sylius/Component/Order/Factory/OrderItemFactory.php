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
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class OrderItemFactory implements OrderItemFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $decoratedFactory;

    /**
     * @var RepositoryInterface
     */
    private $orderRepository;

    /**
     * @param FactoryInterface $decoratedFactory
     * @param RepositoryInterface $orderRepository
     */
    public function __construct(FactoryInterface $decoratedFactory, RepositoryInterface $orderRepository)
    {
        $this->decoratedFactory = $decoratedFactory;
        $this->orderRepository = $orderRepository;
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
    public function createForOrderWithId($orderId)
    {
        $order = $this->orderRepository->find($orderId);
        Assert::notNull($order);

        $orderItem = $this->createNew();
        $orderItem->setOrder($order);

        return $orderItem;
    }
}
