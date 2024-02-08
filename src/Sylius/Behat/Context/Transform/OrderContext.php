<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Webmozart\Assert\Assert;

final class OrderContext implements Context
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository,
        private OrderRepositoryInterface $orderRepository,
    ) {
    }

    /**
     * @Transform :order
     * @Transform /^"([^"]+)" order$/
     * @Transform /^order "([^"]+)"$/
     */
    public function getOrderByNumber(string $orderNumber): OrderInterface
    {
        $orderNumber = $this->getOrderNumber($orderNumber);
        $order = $this->orderRepository->findOneBy(['number' => $orderNumber]);

        Assert::notNull($order, sprintf('Cannot find order with number %s', $orderNumber));

        return $order;
    }

    /**
     * @Transform /^latest order$/
     */
    public function getLatestOrder(): OrderInterface
    {
        $orders = $this->orderRepository->findLatest(1);

        Assert::notEmpty($orders, 'No order have been made');

        return $orders[0];
    }

    /**
     * @Transform /^this order made by "([^"]+)"$/
     * @Transform /^order placed by "([^"]+)"$/
     * @Transform /^the order of "([^"]+)"$/
     */
    public function getOrderByCustomer(string $email): OrderInterface
    {
        $customer = $this->customerRepository->findOneBy(['email' => $email]);
        Assert::notNull($customer, sprintf('Cannot find customer with email %s.', $email));

        $orders = $this->orderRepository->findByCustomer($customer);
        Assert::notEmpty($orders);

        return end($orders);
    }

    /**
     * @Transform :orderNumber
     * @Transform /^an order "([^"]+)"$/
     * @Transform /^another order "([^"]+)"$/
     * @Transform /^the order "([^"]+)"$/
     * @Transform /^the "([^"]+)" order$/
     */
    public function getOrderNumber(string $orderNumber): string
    {
        return str_replace('#', '', $orderNumber);
    }
}
