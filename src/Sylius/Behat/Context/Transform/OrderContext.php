<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Webmozart\Assert\Assert;

final class OrderContext implements Context
{
    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @Transform :order
     */
    public function getOrderByNumber($orderNumber)
    {
        $orderNumber = $this->getOrderNumber($orderNumber);
        $order = $this->orderRepository->findOneBy(['number' => $orderNumber]);

        Assert::notNull($order, sprintf('Cannot find order with number %s', $orderNumber));

        return $order;
    }

    /**
     * @Transform /^latest order$/
     */
    public function getLatestOrder()
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
    public function getOrderByCustomer($email)
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
     * @Transform /^the order "([^"]+)"$/
     * @Transform /^the "([^"]+)" order$/
     */
    public function getOrderNumber($orderNumber)
    {
        return str_replace('#', '', $orderNumber);
    }
}
