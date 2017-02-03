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
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Updater\UnpaidOrdersStateUpdaterInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ManagingOrdersContext implements Context
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
     * @var ObjectManager
     */
    private $orderManager;

    /**
     * @var ProductVariantResolverInterface
     */
    private $variantResolver;

    /**
     * @var UnpaidOrdersStateUpdaterInterface
     */
    private $unpaidOrdersStateUpdater;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param OrderRepositoryInterface $orderRepository
     * @param RepositoryInterface $orderItemRepository
     * @param RepositoryInterface $addressRepository
     * @param RepositoryInterface $adjustmentRepository
     * @param ObjectManager $orderManager
     * @param ProductVariantResolverInterface $variantResolver
     * @param UnpaidOrdersStateUpdaterInterface $unpaidOrdersStateUpdater
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        OrderRepositoryInterface $orderRepository,
        RepositoryInterface $orderItemRepository,
        RepositoryInterface $addressRepository,
        RepositoryInterface $adjustmentRepository,
        ObjectManager $orderManager,
        ProductVariantResolverInterface $variantResolver,
        UnpaidOrdersStateUpdaterInterface $unpaidOrdersStateUpdater
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->orderRepository = $orderRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->addressRepository = $addressRepository;
        $this->adjustmentRepository = $adjustmentRepository;
        $this->orderManager = $orderManager;
        $this->variantResolver = $variantResolver;
        $this->unpaidOrdersStateUpdater = $unpaidOrdersStateUpdater;
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
        $order->setCheckoutCompletedAt(new \DateTime('-'.$amount.' '.$time));
        $this->orderManager->flush();

        $this->unpaidOrdersStateUpdater->cancel();
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
}
