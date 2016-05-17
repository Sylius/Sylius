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
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class OrderContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

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
     * @var RepositoryInterface
     */
    private $adjustmentRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param OrderRepositoryInterface $orderRepository
     * @param RepositoryInterface $orderItemRepository
     * @param RepositoryInterface $addressRepository
     * @param RepositoryInterface $adjustmentRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        OrderRepositoryInterface $orderRepository,
        RepositoryInterface $orderItemRepository,
        RepositoryInterface $addressRepository,
        RepositoryInterface $adjustmentRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->orderRepository = $orderRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->addressRepository = $addressRepository;
        $this->adjustmentRepository = $adjustmentRepository;
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

        $adjustmentsId = [];
        foreach ($order->getAdjustments() as $adjustment) {
            $adjustmentsId[] = $adjustment->getId();
        }

        $this->sharedStorage->set('deleted_adjustments', $adjustmentsId);
        $this->sharedStorage->set('deleted_addresses', [
            $order->getShippingAddress()->getId(),
            $order->getBillingAddress()->getId(),
        ]);

        $this->orderRepository->remove($order);
    }

    /**
     * @Then /^([^"]+) should not exist in the registry$/
     */
    public function orderShouldNotExistInTheRegistry(OrderInterface $order)
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneBy(['number' => $order->getNumber()]);

        expect($order)->toBe(null);
    }

    /**
     * @Then the order item with product :product should not exist
     */
    public function orderItemShouldNotExistInTheRegistry(ProductInterface $product)
    {
        $orderItems = $this->orderItemRepository->findBy(['variant' => $product->getFirstVariant()]);

        expect($orderItems)->toBe([]);
    }

    /**
     * @Then /^billing and shipping addresses of this order should not exist$/
     */
    public function addressesShouldNotExistInTheRegistry()
    {
        $addresses = $this->sharedStorage->get('deleted_adjustments');

        $addresses = $this->addressRepository->findBy(['id' => $addresses]);

        expect($addresses)->toBe([]);
    }

    /**
     * @Then /^adjustments of this order should not exist$/
     */
    public function adjustmentShouldNotExistInTheRegistry()
    {
        $adjustments = $this->sharedStorage->get('deleted_adjustments');

        $adjustments = $this->adjustmentRepository->findBy(['id' => $adjustments]);
        expect($adjustments)->toBe([]);
    }
}
