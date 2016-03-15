<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use PhpSpec\Exception\Example\NotEqualException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class OrderContextSpec extends ObjectBehavior
{
    function let(
        SharedStorageInterface $sharedStorage,
        OrderRepositoryInterface $orderRepository,
        RepositoryInterface $orderItemRepository,
        RepositoryInterface $addressRepository,
        RepositoryInterface $adjustmentRepository
    ) {
        $this->beConstructedWith($sharedStorage, $orderRepository, $orderItemRepository, $addressRepository, $adjustmentRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Domain\OrderContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_deletes_an_order(
        SharedStorageInterface $sharedStorage,
        AddressInterface $shippingAddress,
        AddressInterface $billingAddress,
        AdjustmentInterface $adjustment,
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order
    ) {
        $orderRepository->findOneBy(['number' => '#00000000'])->willReturn($order);
        $order->getBillingAddress()->willReturn($billingAddress);
        $order->getShippingAddress()->willReturn($shippingAddress);
        $order->getAdjustments()->willReturn([$adjustment]);
        $billingAddress->getId()->willReturn(3);
        $shippingAddress->getId()->willReturn(2);
        $adjustment->getId()->willReturn(1);

        $orderRepository->remove($order)->shouldBeCalled();
        $sharedStorage->set('deleted_adjustments', [1])->shouldBeCalled();
        $sharedStorage->set('deleted_addresses', [2, 3])->shouldBeCalled();

        $this->iDeleteTheOrder('#00000000');
    }

    function it_throws_an_exception_when_order_is_not_found(OrderRepositoryInterface $orderRepository)
    {
        $orderRepository->findOneBy(['number' => '#00000000'])->willReturn(null);

        $this->shouldThrow(new \InvalidArgumentException('Order with #00000000 number was not found in an order repository'))->during('iDeleteTheOrder', ['#00000000']);
    }

    function it_checks_if_an_order_exists_in_repository(OrderRepositoryInterface $orderRepository, OrderInterface $order)
    {
        $order->getNumber()->willReturn('#00000000');
        $orderRepository->findOneBy(['number' => '#00000000'])->willReturn(null);

        $this->orderShouldNotExistInTheRegistry($order);
    }

    function it_throws_an_exception_if_order_still_exists(OrderRepositoryInterface $orderRepository, OrderInterface $order)
    {
        $order->getNumber()->willReturn('#00000000');
        $orderRepository->findOneBy(['number' => '#00000000'])->willReturn($order);

        $this->shouldThrow(NotEqualException::class)->during('orderShouldNotExistInTheRegistry', [$order]);
    }

    function it_checks_if_an_order_item_exists_in_repository(
        RepositoryInterface $orderItemRepository,
        ProductInterface $product,
        ProductVariantInterface $productVariant
    ) {
        $product->getMasterVariant()->willReturn($productVariant);

        $orderItemRepository->findBy(['variant' => $productVariant])->willReturn([]);

        $this->orderItemShouldNotExistInTheRegistry($product);
    }

    function it_throws_an_exception_if_order_item_still_exist(
        RepositoryInterface $orderItemRepository,
        ProductInterface $product,
        ProductVariantInterface $productVariant,
        OrderItemInterface $orderItem
    ) {
        $product->getMasterVariant()->willReturn($productVariant);

        $orderItemRepository->findBy(['variant' => $productVariant])->willReturn([$orderItem]);

        $this->shouldThrow(NotEqualException::class)->during('orderItemShouldNotExistInTheRegistry', [$product]);
    }

    function it_checks_if_an_order_addresses_exists_in_repository(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $addressRepository,
        OrderInterface $order
    ) {
        $sharedStorage->get('deleted_adjustments')->willReturn([1, 2]);

        $addressRepository->findBy(['id' => [1, 2]])->willReturn([]);

        $this->addressesShouldNotExistInTheRegistry($order);
    }

    function it_throws_an_exception_if_shipping_addresses_still_exist(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $addressRepository,
        OrderInterface $order,
        AddressInterface $address
    ) {
        $sharedStorage->get('deleted_adjustments')->willReturn([1, 2]);

        $addressRepository->findBy(['id' => [1, 2]])->willReturn([$address]);

        $this->shouldThrow(NotEqualException::class)->during('addressesShouldNotExistInTheRegistry', [$order]);
    }

    function it_throws_an_exception_if_billing_addresses_still_exist(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $addressRepository,
        OrderInterface $order,
        AddressInterface $address
    ) {
        $sharedStorage->get('deleted_adjustments')->willReturn([1, 2]);

        $addressRepository->findBy(['id' => [1, 2]])->willReturn([$address]);

        $this->shouldThrow(NotEqualException::class)->during('addressesShouldNotExistInTheRegistry', [$order]);
    }

    function it_checks_if_an_order_adjustments_exists_in_repository(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $adjustmentRepository
    ) {
        $sharedStorage->get('deleted_adjustments')->willReturn([1]);

        $adjustmentRepository->findBy(['id' => [1]])->willReturn([]);

        $this->adjustmentShouldNotExistInTheRegistry();
    }

    function it_throws_an_exception_if_adjustments_still_exist(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $adjustmentRepository,
        AdjustmentInterface $adjustment
    ) {
        $sharedStorage->get('deleted_adjustments')->willReturn([1]);

        $adjustmentRepository->findBy(['id' => [1]])->willReturn([$adjustment]);

        $this->shouldThrow(NotEqualException::class)->during('adjustmentShouldNotExistInTheRegistry', []);
    }
}
