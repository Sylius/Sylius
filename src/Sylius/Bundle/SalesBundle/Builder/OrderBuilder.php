<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Builder;

use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Bundle\SalesBundle\Model\OrderInterface;
use Sylius\Bundle\SalesBundle\Model\SellableInterface;

/**
 * Order builder.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderBuilder implements OrderBuilderInterface
{
    /**
     * Order repository.
     *
     * @var ObjectRepository
     */
    protected $orderRepository;

    /**
     * Order item repository.
     *
     * @var ObjectRepository
     */
    protected $itemRepository;

    /**
     * Order which is currently under construction.
     *
     * @var OrderInterface
     */
    protected $order;

    /**
     * Constructor.
     *
     * @param ObjectRepository $orderRepository
     * @param ObjectRepository $itemRepository
     */
    public function __construct(ObjectRepository $orderRepository, ObjectRepository $itemRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->itemRepository = $itemRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $this->order = $this->orderRepository->createNew();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function modify(OrderInterface $order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function add(SellableInterface $sellable, $unitPrice, $quantity = 1)
    {
        if (null === $this->order) {
            throw new \LogicException('Cannot add new item to order via builder, before creating it.');
        }

        $item = $this->itemRepository->createNew();

        $item->setSellable($sellable);
        $item->setUnitPrice($unitPrice);
        $item->setQuantity($quantity);

        $this->order->addItem($item);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        if (null === $this->order) {
            throw new \LogicException('Cannot get order from builder, without creating it.');
        }

        $this->order->calculateTotal();

        return $this->order;
    }

    /**
     * Create new order item instance.
     *
     * @return OrderItemInterface
     */
    protected function createNewItem()
    {
        return $this->itemRepository->createNew();
    }
}
