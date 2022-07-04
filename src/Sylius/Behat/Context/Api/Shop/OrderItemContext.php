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

namespace Sylius\Behat\Context\Api\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Webmozart\Assert\Assert;

final class OrderItemContext implements Context
{
    public function __construct(
        private ApiClientInterface $orderItemsClient,
        private ApiClientInterface $orderItemUnitsClient,
        private ResponseCheckerInterface $responseChecker,
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @When I try to see one of the items from the order placed by a customer :customer
     */
    public function iTryToSeeOneOfTheItemsFromTheOrderPlacedByACustomer(CustomerInterface $customer): void
    {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');
        Assert::eq($order->getCustomer(), $customer);

        /** @var OrderItemInterface $orderItem */
        $orderItem = $order->getItems()->first();

        $this->orderItemsClient->show((string) $orderItem->getId());
    }

    /**
     * @When I try to see one of the units from the order placed by a customer :customer
     */
    public function iTryToSeeOneOfTheUnitsFromTheOrderPlacedByACustomer(CustomerInterface $customer): void
    {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');
        Assert::eq($order->getCustomer(), $customer);

        /** @var OrderItemUnitInterface $orderItemUnit */
        $orderItemUnit = $order->getItemUnits()->first();

        $this->orderItemUnitsClient->show((string) $orderItemUnit->getId());
    }

    /**
     * @Then I should not be able to see that item
     */
    public function iShouldNotBeAbleToSeeThatItem(): void
    {
        Assert::false($this->responseChecker->isShowSuccessful($this->orderItemsClient->getLastResponse()));
    }

    /**
     * @Then I should not be able to see that unit
     */
    public function iShouldNotBeAbleToSeeThatUnit(): void
    {
        Assert::false($this->responseChecker->isShowSuccessful($this->orderItemUnitsClient->getLastResponse()));
    }
}
