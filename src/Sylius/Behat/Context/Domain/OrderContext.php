<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class OrderContext implements Context
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var RepositoryInterface
     */
    private $orderItemRepository;

    /**
     * @var RepositoryInterface
     */
    private $addressRepository;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param RepositoryInterface $orderItemRepository
     * @param RepositoryInterface $addressRepository
     */
    public function __construct(OrderRepositoryInterface $orderRepository, RepositoryInterface $orderItemRepository, RepositoryInterface $addressRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->addressRepository = $addressRepository;
    }

    /**
     * @When I delete the order :orderNumber
     */
    public function iDeleteTheOrder($orderNumber)
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneBy(['number' => $orderNumber]);
        if (null === $order) {
            throw new \InvalidArgumentException(sprintf('Order with %s number was not found in an order repository', $orderNumber));
        }

        $this->orderRepository->remove($order);
    }

    /**
     * @Then /^([^"]+) should not exist in the registry$/
     */
    public function orderShouldNotExistInTheRegistry(OrderInterface $order)
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->find($order->getId());

        expect($order)->toBe(null);
    }

    /**
     * @Then the order item with product :product should not exist
     */
    public function orderItemShouldNotExistInTheRegistry(ProductInterface $product)
    {
        $orderItems = $this->orderItemRepository->findBy(['variant' => $product->getMasterVariant()]);

        expect($orderItems)->toBe([]);
    }

    /**
     * @Then /^billing and shipping addresses of ([^"]+) should not exist$/
     */
    public function addressesShouldNotExistInTheRegistry(OrderInterface $order)
    {
        $billingAddress = $order->getBillingAddress();
        $shippingAddress = $order->getShippingAddress();

        $billingAddress = $this->addressRepository->find($billingAddress->getId());
        $shippingAddress = $this->addressRepository->find($shippingAddress->getId());

        expect($billingAddress)->toBe(null);
        expect($shippingAddress)->toBe(null);
    }
}
