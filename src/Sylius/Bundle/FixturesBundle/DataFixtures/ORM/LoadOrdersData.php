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
use Sylius\Bundle\AddressingBundle\Model\AddressInterface;
use Sylius\Bundle\CartBundle\SyliusCartEvents;
use Sylius\Bundle\CoreBundle\Checkout\SyliusCheckoutEvents;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\CoreBundle\Model\OrderItemInterface;
use Sylius\Bundle\CoreBundle\Model\ShipmentInterface;
use Sylius\Bundle\OrderBundle\SyliusOrderEvents;
use Sylius\Bundle\PaymentsBundle\Model\PaymentInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class LoadOrdersData extends DataFixture
{
    public function load(ObjectManager $manager)
    {
        $orderRepository = $this->getOrderRepository();
        $orderItemRepository = $this->getOrderItemRepository();

        for ($i = 1; $i <= 50; $i++) {
            /* @var $order OrderInterface */
            $order = $orderRepository->createNew();

            for ($j = 0; $j <= rand(3, 6); $j++) {
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
            $order->setUser($this->getReference('Sylius.User-'.rand(1, 15)));
            $order->setShippingAddress($this->createAddress());
            $order->setBillingAddress($this->createAddress());
            $order->setCreatedAt($this->faker->dateTimeBetween('1 year ago', 'now'));

            $this->dispatchEvents($order);

            $order->calculateTotal();
            $order->complete();

            $this->createPayment($order);

            $this->setReference('Sylius.Order-'.$i, $order);

            $manager->persist($order);
            $manager->flush($order);
        }
    }

    protected function createAddress()
    {
        /* @var $address AddressInterface */
        $address = $this->getAddressRepository()->createNew();
        $address->setFirstname($this->faker->firstName);
        $address->setLastname($this->faker->lastName);
        $address->setCity($this->faker->city);
        $address->setStreet($this->faker->streetAddress);
        $address->setPostcode($this->faker->postcode);

        do {
            $isoName = $this->faker->countryCode;
        } while ('UK' === $isoName);

        $country = $this->getReference('Sylius.Country.'.$isoName);
        $province = $country->hasProvinces() ? $this->faker->randomElement($country->getProvinces()->toArray()) : null;

        $address->setCountry($country);
        $address->setProvince($province);

        return $address;
    }

    protected function createPayment(OrderInterface $order)
    {
        /* @var $payment PaymentInterface */
        $payment = $this->getPaymentRepository()->createNew();
        $payment->setMethod($this->getReference('Sylius.PaymentMethod.Stripe'));
        $payment->setAmount($order->getTotal($order));
        $payment->setCurrency($order->getCurrency());
        $payment->setState($this->getPaymentState());

        $order->setPayment($payment);

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
        $dispatcher->dispatch(SyliusOrderEvents::PRE_CREATE, new GenericEvent($order));
    }

    protected function getPaymentState()
    {
        return array_rand(array_flip(array(
            PaymentInterface::STATE_COMPLETED,
            PaymentInterface::STATE_FAILED,
            PaymentInterface::STATE_NEW,
            PaymentInterface::STATE_PENDING,
            PaymentInterface::STATE_PROCESSING,
            PaymentInterface::STATE_UNKNOWN,
            PaymentInterface::STATE_VOID,
            PaymentInterface::STATE_CANCELLED,
            PaymentInterface::STATE_REFUNDED,
        )));
    }

    protected function getShipmentState()
    {
        return array_rand(array_flip(array(
            ShipmentInterface::STATE_CHECKOUT,
            ShipmentInterface::STATE_SHIPPED,
            ShipmentInterface::STATE_PENDING,
            ShipmentInterface::STATE_READY,
            ShipmentInterface::STATE_RETURNED,
            ShipmentInterface::STATE_CANCELLED,
        )));
    }

    public function getOrder()
    {
        return 7;
    }
}
