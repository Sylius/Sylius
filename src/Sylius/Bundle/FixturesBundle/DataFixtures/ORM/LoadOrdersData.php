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
        $orderFactory = $this->getOrderFactory();
        $orderItemFactory = $this->getOrderItemFactory();
        $orderItemQuantityModifier = $this->get('sylius.order_item_quantity_modifier');

        $currencyExchangeRates = [
            'GBP' => 0.8,
            'USD' => 1.2,
            'EUR' => 1.0,
        ];

        for ($i = 1; $i <= 15; ++$i) {
            /* @var $order OrderInterface */
            $order = $orderFactory->createNew();
            $channel = $this->getReference('Sylius.Channel.DEFAULT');

            $order->setChannel($channel);

            for ($j = 0, $items = rand(3, 6); $j <= $items; ++$j) {
                $variant = $this->getReference('Sylius.Variant-'.rand(1, SYLIUS_FIXTURES_TOTAL_VARIANTS - 1));

                /* @var $item OrderItemInterface */
                $item = $orderItemFactory->createNew();
                $item->setVariant($variant);
                $item->setUnitPrice($variant->getPrice());

                $orderItemQuantityModifier->modify($item, mt_rand(1, 5));

                $order->addItem($item);
            }

            $this->createShipment($order);

            $order->setCurrency($this->faker->randomElement(array_keys($currencyExchangeRates)));
            $order->setExchangeRate($currencyExchangeRates[$order->getCurrency()]);
            $order->setShippingAddress($this->createAddress());
            $order->setBillingAddress($this->createAddress());
            $order->setCreatedAt($this->faker->dateTimeBetween('1 year ago', 'now'));

            $this->dispatchEvents($order);

            $order->complete();
            $paymentState = $this->faker->boolean(50) ? PaymentInterface::STATE_COMPLETED : PaymentInterface::STATE_NEW;

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
        return 60;
    }

    /**
     * @param OrderInterface $order
     * @param null|string    $state
     */
    protected function createPayment(OrderInterface $order, $state = null)
    {
        /* @var $payment PaymentInterface */
        $payment = $this->getPaymentFactory()->createNew();
        $payment->setOrder($order);
        $payment->setMethod($this->getReference('Sylius.PaymentMethod.offline'));
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
        $shipment = $this->getShipmentFactory()->createNew();
        $shipment->setMethod($this->getReference('Sylius.ShippingMethod.ups_ground'));
        $shipment->setState($this->faker->boolean(50) ? ShipmentInterface::STATE_READY : ShipmentInterface::STATE_SHIPPED);

        foreach ($order->getItemUnits() as $item) {
            $shipment->addUnit($item);
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
}
