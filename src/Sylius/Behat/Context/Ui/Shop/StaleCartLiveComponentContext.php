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

namespace Sylius\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Behat\Mink\Mink;
use Behat\Step\Given;
use Behat\Step\Then;
use Behat\Step\When;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Webmozart\Assert\Assert;

final readonly class StaleCartLiveComponentContext implements Context
{
    public function __construct(
        private Mink $mink,
        private SharedStorageInterface $sharedStorage,
        private OrderRepositoryInterface $orderRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    #[Given('I note the current order id for later assertions')]
    public function iNoteTheCurrentOrderId(): void
    {
        $page = $this->mink->getSession()->getPage();
        $component = $page->find('css', '[data-live-name-value="sylius_shop:cart:form"]');
        Assert::notNull($component, 'Cart LiveComponent root element not found on page.');

        $rawProps = $component->getAttribute('data-live-props-value');
        Assert::notNull($rawProps, 'data-live-props-value attribute missing on cart form LiveComponent.');

        $props = json_decode($rawProps, true);
        $orderId = $props['resource'] ?? null;
        Assert::notNull($orderId, 'Order ID not found in dehydrated LiveComponent props.');

        $this->sharedStorage->set('stale_order_id', (int) $orderId);
    }

    #[When('the order is completed in the background')]
    public function theOrderIsCompletedInTheBackground(): void
    {
        $orderId = $this->sharedStorage->get('stale_order_id');

        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->find($orderId);
        Assert::notNull($order, sprintf('Order #%d not found.', $orderId));

        $order->setCheckoutState(OrderCheckoutStates::STATE_COMPLETED);
        $order->setState(BaseOrderInterface::STATE_NEW);

        $this->entityManager->flush();
    }

    #[When('I clear my cart from the stale page without reloading')]
    public function iClearMyCartFromTheStalePage(): void
    {
        $page = $this->mink->getSession()->getPage();

        $button = $page->find('css', '[data-test-clear-cart]')
            ?? $page->find('css', '[data-live-action-param="clearCart"]');

        Assert::notNull($button, 'Clear cart button not found on the stale page.');

        $button->click();

        $this->waitForLiveComponent();
    }

    #[When('I remove first item from cart from the stale page without reloading')]
    public function iRemoveFirstItemFromCartFromTheStalePageWithoutReloading(): void
    {
        $page = $this->mink->getSession()->getPage();

        $button = $page->find('css', '[data-test-remove-cart-item]')
            ?? $page->find('css', '[data-live-action-param="removeItem"]');

        Assert::notNull($button, 'Remove item button not found on the stale page.');

        $button->click();

        $this->waitForLiveComponent();
    }

    #[Then('the completed order should still exist in the database')]
    public function theCompletedOrderShouldStillExistInTheDatabase(): void
    {
        $orderId = $this->sharedStorage->get('stale_order_id');

        // Clear the identity map to bypass any cached entity and force a real SELECT.
        $this->entityManager->clear();

        $order = $this->orderRepository->find($orderId);

        Assert::notNull(
            $order,
            sprintf('Order #%d was deleted. The stale LiveComponent action must not remove a completed order.', $orderId),
        );
    }

    #[Then('the order checkout state should be :expectedState')]
    public function theOrderCheckoutStateShouldBe(string $expectedState): void
    {
        $orderId = $this->sharedStorage->get('stale_order_id');
        $this->entityManager->clear();

        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->find($orderId);
        Assert::notNull($order);

        Assert::same(
            $order->getCheckoutState(),
            $expectedState,
            sprintf('Checkout state: expected "%s" but got "%s".', $expectedState, $order->getCheckoutState()),
        );
    }

    #[When('I increase the quantity of the first cart item to :quantity on the stale page without reloading')]
    public function iIncreaseTheQuantityOfTheFirstCartItemOnTheStalePage(int $quantity): void
    {
        $page = $this->mink->getSession()->getPage();

        $input = $page->find('css', '[data-test-cart-item-quantity]');
        Assert::notNull($input, 'Cart item quantity input not found on the stale page.');

        $input->setValue((string) $quantity);

        $this->waitForLiveComponent();
    }

    #[Then('the order item quantity should still be :quantity')]
    public function theOrderItemQuantityShouldStillBe(int $quantity): void
    {
        $orderId = $this->sharedStorage->get('stale_order_id');
        $this->entityManager->clear();

        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->find($orderId);
        Assert::notNull($order);

        $firstItem = $order->getItems()->first();
        Assert::notFalse($firstItem, 'The order has no items.');

        Assert::same(
            $firstItem->getQuantity(),
            $quantity,
            sprintf('Expected item quantity %d but got %d.', $quantity, $firstItem->getQuantity()),
        );
    }

    #[Then('the order should still have :count item(s)')]
    public function theOrderShouldStillHaveItems(int $count): void
    {
        $orderId = $this->sharedStorage->get('stale_order_id');
        $this->entityManager->clear();

        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->find($orderId);
        Assert::notNull($order);

        Assert::count(
            $order->getItems(),
            $count,
            sprintf('Expected %d item(s) but found %d.', $count, $order->getItems()->count()),
        );
    }

    private function waitForLiveComponent(int $timeoutMs = 5000): void
    {
        $this->mink->getSession()->wait(2000, "document.querySelector('[data-live-loading]') !== null");
        $this->mink->getSession()->wait($timeoutMs, "document.querySelector('[data-live-loading]') === null");
    }
}
