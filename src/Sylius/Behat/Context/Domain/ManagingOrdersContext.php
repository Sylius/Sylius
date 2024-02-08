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

namespace Sylius\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Updater\UnpaidOrdersStateUpdaterInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class ManagingOrdersContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private OrderRepositoryInterface $orderRepository,
        private RepositoryInterface $orderItemRepository,
        private RepositoryInterface $addressRepository,
        private RepositoryInterface $adjustmentRepository,
        private ObjectManager $orderManager,
        private ProductVariantResolverInterface $variantResolver,
        private UnpaidOrdersStateUpdaterInterface $unpaidOrdersStateUpdater,
    ) {
    }

    /**
     * @When I delete the order :order
     */
    public function iDeleteTheOrder(OrderInterface $order)
    {
        $adjustmentsId = [];
        foreach ($order->getAdjustments() as $adjustment) {
            $adjustmentsId[] = $adjustment->getId();
        }

        $this->sharedStorage->set('deleted_adjustments', $adjustmentsId);
        $this->sharedStorage->set('deleted_addresses', [
            $order->getShippingAddress()->getId(),
            $order->getBillingAddress()->getId(),
        ]);

        $this->sharedStorage->set('order_id', $order->getId());
        $this->orderRepository->remove($order);
    }

    /**
     * @When I view the summary of the order :order
     */
    public function iViewTheSummaryOfTheOrder(OrderInterface $order): void
    {
        $this->sharedStorage->set('order', $order);
    }

    /**
     * @Then this order should not exist in the registry
     */
    public function orderShouldNotExistInTheRegistry()
    {
        $orderId = $this->sharedStorage->get('order_id');
        $order = $this->orderRepository->find($orderId);

        Assert::null($order);
    }

    /**
     * @Then the order item with product :product should not exist
     */
    public function orderItemShouldNotExistInTheRegistry(ProductInterface $product)
    {
        $orderItems = $this->orderItemRepository->findBy(['variant' => $this->variantResolver->getVariant($product)]);

        Assert::same($orderItems, []);
    }

    /**
     * @Then billing and shipping addresses of this order should not exist
     */
    public function addressesShouldNotExistInTheRegistry()
    {
        $addresses = $this->sharedStorage->get('deleted_addresses');

        $addresses = $this->addressRepository->findBy(['id' => $addresses]);

        Assert::same($addresses, []);
    }

    /**
     * @Then adjustments of this order should not exist
     */
    public function adjustmentShouldNotExistInTheRegistry()
    {
        $adjustments = $this->sharedStorage->get('deleted_adjustments');

        $adjustments = $this->adjustmentRepository->findBy(['id' => $adjustments]);

        Assert::same($adjustments, []);
    }

    /**
     * @Given /^(this order) has not been paid for (\d+) (day|days|hour|hours)$/
     */
    public function thisOrderHasNotBeenPaidForDays(OrderInterface $order, $amount, $time)
    {
        $order->setCheckoutCompletedAt(new \DateTime('-' . $amount . ' ' . $time));
        $this->orderManager->flush();

        $this->unpaidOrdersStateUpdater->cancel();
    }

    /**
     * @Given /^the (order "[^"]+") has not been paid for (\d+) (day|days)$/
     */
    public function orderWithNumberHasNotBeenPaidForDays(OrderInterface $order, int $amount, string $days): void
    {
        $order->setCheckoutCompletedAt(new \DateTime(sprintf('-%d %s', $amount, $days)));

        $this->orderManager->flush();
    }

    /**
     * @Then /^(this order) should be automatically cancelled$/
     */
    public function thisOrderShouldBeAutomaticallyCancelled(OrderInterface $order)
    {
        Assert::same($order->getState(), OrderInterface::STATE_CANCELLED);
    }

    /**
     * @Then /^(this order) should not be cancelled$/
     */
    public function thisOrderShouldNotBeCancelled(OrderInterface $order)
    {
        Assert::notSame($order->getState(), OrderInterface::STATE_CANCELLED);
    }

    /**
     * @Then /^(the order)'s items total should be ("[^"]+")$/
     */
    public function theOrdersItemsTotalShouldBe(OrderInterface $order, int $itemsTotal): void
    {
        Assert::same($order->getItemsTotal(), $itemsTotal);
    }

    /**
     * @Then /^there should be a shipping charge ("[^"]+") for "([^"]+)" method$/
     */
    public function thereShouldBeAShippingChargeForMethod(int $shippingCharge, string $shippingMethodName): void
    {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');
        foreach ($order->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT) as $adjustment) {
            if ($adjustment->getAmount() === $shippingCharge && $adjustment->getDetails()['shippingMethodName'] === $shippingMethodName) {
                return;
            }
        }

        throw new \DomainException('The given order has no shipping adjustment with proper amount and method');
    }

    /**
     * @Then /^there should be a shipping tax ("[^"]+") for "([^"]+)" method$/
     */
    public function thereShouldBeAShippingTaxForMethod(int $shippingTax, string $shippingMethodName): void
    {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');
        foreach ($order->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT) as $adjustment) {
            if ($adjustment->getAmount() === $shippingTax && $adjustment->getDetails()['shippingMethodName'] === $shippingMethodName) {
                return;
            }
        }

        throw new \DomainException('The given order has no shipping adjustment with proper amount and method');
    }

    /**
     * @Then /^(the order)'s shipping total should be ("[^"]+")$/
     */
    public function theOrdersShippingTotalShouldBe(OrderInterface $order, int $shippingTotal): void
    {
        Assert::same($order->getShippingTotal(), $shippingTotal);
    }

    /**
     * @Then /^(the order)'s tax total should be ("[^"]+")$/
     */
    public function theOrdersTaxTotalShouldBe(OrderInterface $order, int $taxTotal): void
    {
        Assert::same($order->getTaxTotal(), $taxTotal);
    }

    /**
     * @Then /^(the order)'s total should be ("[^"]+")$/
     */
    public function theOrdersTotalShouldBe(OrderInterface $order, int $total): void
    {
        Assert::same($order->getTotal(), $total);
    }
}
