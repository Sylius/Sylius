<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\OrderProcessing\OrderShipmentFactoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Payment\Factory\PaymentFactoryInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\User\Model\CustomerInterface;

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
     * @var FactoryInterface
     */
    private $orderFactory;

    /**
     * @var OrderShipmentFactoryInterface
     */
    private $orderShipmentFactory;

    /**
     * @var PaymentFactoryInterface
     */
    private $paymentFactory;

    /**
     * @var FactoryInterface
     */
    private $orderItemFactory;

    /**
     * @var OrderItemQuantityModifierInterface
     */
    private $itemQuantityModifier;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param FactoryInterface $orderFactory
     * @param OrderShipmentFactoryInterface $orderShipmentFactory
     * @param PaymentFactoryInterface $paymentFactory
     * @param FactoryInterface $orderItemFactory
     * @param OrderItemQuantityModifierInterface $itemQuantityModifier
     * @param SharedStorageInterface $sharedStorage
     * @param ObjectManager $objectManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        OrderRepositoryInterface $orderRepository,
        FactoryInterface $orderFactory,
        OrderShipmentFactoryInterface $orderShipmentFactory,
        PaymentFactoryInterface $paymentFactory,
        FactoryInterface $orderItemFactory,
        OrderItemQuantityModifierInterface $itemQuantityModifier,
        ObjectManager $objectManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->orderRepository = $orderRepository;
        $this->orderFactory = $orderFactory;
        $this->orderShipmentFactory = $orderShipmentFactory;
        $this->paymentFactory = $paymentFactory;
        $this->orderItemFactory = $orderItemFactory;
        $this->itemQuantityModifier = $itemQuantityModifier;
        $this->objectManager = $objectManager;
    }

    /**
     * @Given the customer :customer placed an order :orderNumber
     */
    public function theCustomerPlacedAnOrder(CustomerInterface $customer, $orderNumber)
    {
        /** @var OrderInterface $order */
        $order = $this->orderFactory->createNew();

        $order->setCustomer($customer);
        $order->setNumber($orderNumber);
        $order->setChannel($this->sharedStorage->get('channel'));
        $order->setCurrency($this->sharedStorage->get('currency'));

        $this->sharedStorage->set('order', $order);

        $this->orderRepository->add($order);
    }

    /**
     * @Given /^the customer chose ("[^"]+" shipping method) (to "[^"]+") with ("[^"]+" payment)$/
     */
    public function theCustomerChoseShippingToWithPayment(
        ShippingMethodInterface $shippingMethod,
        AddressInterface $address,
        PaymentMethodInterface $paymentMethod
    ) {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');

        $this->orderShipmentFactory->createForOrder($order);
        $order->getShipments()->first()->setMethod($shippingMethod);

        $payment = $this->paymentFactory->createWithAmountAndCurrency($order->getTotal(), $order->getCurrency());
        $payment->setMethod($paymentMethod);

        $order->addPayment($payment);

        $order->setShippingAddress($address);
        $order->setBillingAddress($address);

        $this->objectManager->flush();
    }

    /**
     * @Given the customer bought single :product
     */
    public function theCustomerBoughtSingle(ProductInterface $product)
    {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');

        /** @var OrderItemInterface $item */
        $item = $this->orderItemFactory->createNew();
        $item->setVariant($product->getMasterVariant());
        $item->setUnitPrice($product->getPrice());

        $this->itemQuantityModifier->modify($item, 1);

        $order->addItem($item);

        $this->objectManager->flush();
    }
}
