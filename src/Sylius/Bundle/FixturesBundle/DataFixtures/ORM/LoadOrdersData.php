<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use Sylius\Component\Cart\SyliusCartEvents;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\SyliusCheckoutEvents;
use Sylius\Component\Order\OrderTransitions;
use Symfony\Component\EventDispatcher\GenericEvent;

class LoadOrdersData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $orderRepository = $this->getOrderRepository();
        $orderItemRepository = $this->getOrderItemRepository();

        $channels = array(
            'WEB-UK',
            'WEB-DE',
            'WEB-US',
            'MOBILE',
        );

        for ($i = 1; $i <= 50; $i++) {
            /* @var $order OrderInterface */
            $order = $orderRepository->createNew();
            $channel = $this->getReference('Sylius.Channel.'.$this->faker->randomElement($channels));

            $order->setChannel($channel);

            for ($j = 0, $items = rand(3, 6); $j <= $items; $j++) {
                $variant = $this->getReference('Sylius.Variant-'.rand(1, SYLIUS_FIXTURES_TOTAL_VARIANTS - 1));

                /* @var $item OrderItemInterface */
                $item = $orderItemRepository->createNew();
                $item->setVariant($variant);
                $item->setUnitPrice($variant->getPrice());
                $item->setQuantity(rand(1, 5));

                $order->addItem($item);
            }

            $this->createShipment($order);

            $order->setCurrency($this->faker->randomElement(array('EUR', 'USD', 'GBP')));
            $order->setShippingAddress($this->createAddress());
            $order->setBillingAddress($this->createAddress());
            $order->setCreatedAt($this->faker->dateTimeBetween('1 year ago', 'now'));

            $this->dispatchEvents($order);

            $order->calculateTotal();
            $order->complete();

            $paymentState = PaymentInterface::STATE_COMPLETED;
            if (rand(1, 10) < 5) {
                $paymentState = PaymentInterface::STATE_NEW;
            }
            $order->setCustomer($this->getReference('Sylius.Customer-'.rand(1, 15)));
            $this->createPayment($order, $paymentState);

            $order->setCompletedAt($this->faker->dateTimeThisDecade);
            $this->setReference('Sylius.Order-'.$i, $order);

            $manager->persist($order);
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 7;
    }

    /**
     * @param OrderInterface $order
     * @param null|string    $state
     */
    protected function createPayment(OrderInterface $order, $state = null)
    {
        /* @var $payment PaymentInterface */
        $payment = $this->getPaymentRepository()->createNew();
        $payment->setOrder($order);
        $payment->setMethod($this->getReference('Sylius.PaymentMethod.StripeCheckout'));
        $payment->setAmount($order->getTotal());
        $payment->setCurrency($order->getCurrency());
        $payment->setState(null === $state ? $this->getPaymentState() : $state);
        $payment->setDetails($this->faker->creditCardDetails());

        $order->addPayment($payment);

        $this->get('event_dispatcher')->dispatch(SyliusCheckoutEvents::FINALIZE_PRE_COMPLETE, new GenericEvent($order));
    }

    protected function createShipment(OrderInterface $order)
    {
        /* @var $shipment ShipmentInterface */
        $shipment = $this->getShipmentRepository()->createNew();
        $shipment->setMethod($this->getReference('Sylius.ShippingMethod.UPS Ground'));
        $shipment->setState($this->getShipmentState());

        foreach ($order->getInventoryUnits() as $item) {
            $shipment->addItem($item);
        }

        $order->addShipment($shipment);
    }

    protected function dispatchEvents($order)
    {
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch(SyliusCartEvents::CART_CHANGE, new GenericEvent($order));
        $dispatcher->dispatch(SyliusCheckoutEvents::SHIPPING_PRE_COMPLETE, new GenericEvent($order));
        $this->get('sm.factory')->get($order, OrderTransitions::GRAPH)->apply(OrderTransitions::SYLIUS_CREATE);
    }

    protected function getPaymentState()
    {
        return array_rand(array_flip(array(
            PaymentInterface::STATE_COMPLETED,
            PaymentInterface::STATE_FAILED,
            PaymentInterface::STATE_NEW,
            PaymentInterface::STATE_PENDING,
            PaymentInterface::STATE_PROCESSING,
            PaymentInterface::STATE_VOID,
            PaymentInterface::STATE_CANCELLED,
            PaymentInterface::STATE_REFUNDED,
            PaymentInterface::STATE_UNKNOWN,
        )));
    }

    protected function getShipmentState()
    {
        return array_rand(array_flip(array(
            ShipmentInterface::STATE_PENDING,
            ShipmentInterface::STATE_ONHOLD,
            ShipmentInterface::STATE_CHECKOUT,
            ShipmentInterface::STATE_SHIPPED,
            ShipmentInterface::STATE_READY,
            ShipmentInterface::STATE_BACKORDERED,
            ShipmentInterface::STATE_RETURNED,
            ShipmentInterface::STATE_CANCELLED,
        )));
    }
}
