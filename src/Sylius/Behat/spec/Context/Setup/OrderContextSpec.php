<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\OrderProcessing\OrderShipmentFactoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Payment\Factory\PaymentFactoryInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class OrderContextSpec extends ObjectBehavior
{
    function let(
        SharedStorageInterface $sharedStorage,
        FactoryInterface $orderFactory,
        OrderRepositoryInterface $orderRepository,
        OrderShipmentFactoryInterface $orderShipmentFactory,
        PaymentFactoryInterface $paymentFactory,
        FactoryInterface $orderItemFactory,
        OrderItemQuantityModifierInterface $itemQuantityModifier,
        ObjectManager $objectManager
    ) {
        $this->beConstructedWith(
            $sharedStorage,
            $orderRepository,
            $orderFactory,
            $orderShipmentFactory,
            $paymentFactory,
            $orderItemFactory,
            $itemQuantityModifier,
            $objectManager
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Setup\OrderContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_creates_customers_order(
        CustomerInterface $customer,
        ChannelInterface $channel,
        CurrencyInterface $currency,
        FactoryInterface $orderFactory,
        OrderInterface $order,
        OrderRepositoryInterface $orderRepository,
        SharedStorageInterface $sharedStorage
    ) {
        $sharedStorage->get('channel')->willReturn($channel);
        $sharedStorage->get('currency')->willReturn($currency);

        $orderFactory->createNew()->willReturn($order);

        $order->setCustomer($customer)->shouldBeCalled();
        $order->setChannel($channel)->shouldBeCalled();
        $order->setCurrency($currency)->shouldBeCalled();
        $order->setNumber('#00000022')->shouldBeCalled();

        $orderRepository->add($order)->shouldBeCalled();
        $sharedStorage->set('order', $order)->shouldBeCalled();

        $this->theCustomerPlacedAnOrder($customer, '#00000022');
    }

    function it_adds_shipping_payment_and_addressing_info_to_an_order(
        AddressInterface $address,
        Collection $shipmentCollection,
        OrderInterface $order,
        OrderShipmentFactoryInterface $orderShipmentFactory,
        PaymentFactoryInterface $paymentFactory,
        PaymentInterface $payment,
        PaymentMethodInterface $paymentMethod,
        SharedStorageInterface $sharedStorage,
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod,
        ObjectManager $objectManager
    ) {
        $sharedStorage->get('order')->willReturn($order);

        $order->getCurrency()->willReturn('EUR');
        $order->getTotal()->willReturn(1234);
        $order->getShipments()->willReturn($shipmentCollection);

        $shipmentCollection->first()->willReturn($shipment);

        $paymentFactory->createWithAmountAndCurrency(1234, 'EUR')->willReturn($payment);

        $order->setBillingAddress($address)->shouldBeCalled();
        $order->setShippingAddress($address)->shouldBeCalled();
        $order->addPayment($payment)->shouldBeCalled();
        $payment->setMethod($paymentMethod)->shouldBeCalled();
        $shipment->setMethod($shippingMethod)->shouldBeCalled();
        $orderShipmentFactory->createForOrder($order)->shouldBeCalled();

        $objectManager->flush()->shouldBeCalled();

        $this->theCustomerChoseShippingToWithPayment($shippingMethod, $address, $paymentMethod);
    }

    function it_adds_single_item_by_customer(
        FactoryInterface $orderItemFactory,
        OrderInterface $order,
        OrderItemInterface $item,
        OrderItemQuantityModifierInterface $itemQuantityModifier,
        ProductInterface $product,
        SharedStorageInterface $sharedStorage,
        ProductVariantInterface $variant,
        ObjectManager $objectManager
    ) {
        $sharedStorage->get('order')->willReturn($order);

        $orderItemFactory->createNew()->willReturn($item);

        $product->getMasterVariant()->willReturn($variant);
        $product->getPrice()->willReturn(1234);

        $itemQuantityModifier->modify($item, 1)->shouldBeCalled();

        $item->setVariant($variant)->shouldBeCalled();
        $item->setUnitPrice(1234)->shouldBeCalled();

        $order->addItem($item)->shouldBeCalled();

        $objectManager->flush()->shouldBeCalled();

        $this->theCustomerBoughtSingle($product);
    }
}
