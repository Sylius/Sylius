<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\AddressingBundle\Model\AddressInterface;
use Sylius\Bundle\CartBundle\SyliusCartEvents;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\CoreBundle\Model\OrderItemInterface;
use Sylius\Bundle\CoreBundle\Model\ShipmentInterface;
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

            /* @var $shipment ShipmentInterface */
            $shipment = $this->getShipmentRepository()->createNew();
            $shipment->setMethod($this->getReference('Sylius.ShippingMethod.UPS Ground'));
            $shipment->setState($this->getShipmentState());

            foreach ($order->getInventoryUnits() as $item) {
                $shipment->addItem($item);
            }

            $order->addShipment($shipment);

            $order->setNumber(str_pad((int) $i, 9, 0, STR_PAD_LEFT));
            $order->setCurrency($this->faker->randomElement(array('EUR', 'USD', 'GBP')));
            $order->setUser($this->getReference('Sylius.User-'.rand(1, 15)));
            $order->setShippingAddress($this->createAddress());
            $order->setBillingAddress($this->createAddress());
            $order->setCreatedAt($this->faker->dateTimeBetween('1 year ago', 'now'));

            $this->dispatchEvents($order);

            $order->calculateTotal();
            $order->complete();

            /* @var $payment PaymentInterface */
            $payment = $this->getPaymentRepository()->createNew();
            $payment->setMethod($this->getReference('Sylius.PaymentMethod.Stripe'));
            $payment->setAmount($order->getTotal());
            $payment->setCurrency($order->getCurrency());
            $payment->setState($this->getPaymentState());

            $order->setPayment($payment);

            $this->setReference('Sylius.Order-'.$i, $order);

            $manager->persist($order);
        }

        $manager->flush();
    }

    private function createAddress()
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

    private function dispatchEvents($order)
    {
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch(SyliusCartEvents::CART_CHANGE, new GenericEvent($order));
        $dispatcher->dispatch('sylius.checkout.shipping.pre_complete', new GenericEvent($order));
        $dispatcher->dispatch('sylius.order.pre_create', new GenericEvent($order));
        $dispatcher->dispatch('sylius.checkout.finalize.pre_complete', new GenericEvent($order));
    }

    private function getPaymentState()
    {
        return array_rand(array_flip(array(
            PaymentInterface::STATE_CHECKOUT,
            PaymentInterface::STATE_COMPLETED,
            PaymentInterface::STATE_FAILED,
            PaymentInterface::STATE_NEW,
            PaymentInterface::STATE_PENDING,
            PaymentInterface::STATE_PROCESSING,
            PaymentInterface::STATE_UNKNOWN,
            PaymentInterface::STATE_VOID,
        )));
    }

    private function getShipmentState()
    {
        return array_rand(array_flip(array(
            ShipmentInterface::STATE_CHECKOUT,
            ShipmentInterface::STATE_DISPATCHED,
            ShipmentInterface::STATE_SHIPPED,
            ShipmentInterface::STATE_READY,
            ShipmentInterface::STATE_PENDING,
            ShipmentInterface::STATE_RETURNED,
        )));
    }

    public function getOrder()
    {
        return 7;
    }
}
