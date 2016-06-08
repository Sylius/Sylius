<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Form\EventSubscriber;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Form\EventSubscriber\AvailableShippingMethodsSubscriber;
use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;

/**
 * @mixin AvailableShippingMethodsSubscriber
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class AvailableShippingMethodsSubscriberSpec extends ObjectBehavior
{
    function let(ZoneMatcherInterface $zoneMatcher)
    {
        $this->beConstructedWith($zoneMatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Form\EventSubscriber\AvailableShippingMethodsSubscriber');
    }

    function it_implements_event_subscriber_interface()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_adds_shipping_methods_field_based_on_event_data(
        AddressInterface $address,
        FormEvent $event,
        FormInterface $form,
        OrderInterface $order,
        ZoneInterface $firstZone,
        ZoneInterface $secondZone,
        ZoneMatcherInterface $zoneMatcher
    ) {
        $event->getData()->willReturn($order);
        $event->getForm()->willReturn($form);

        $order->getShippingAddress()->willReturn($address);
        $zoneMatcher->matchAll($address)->willReturn([$firstZone, $secondZone]);

        $firstZone->getId()->willReturn(1);
        $secondZone->getId()->willReturn(4);

        $form
            ->add('shipments', 'collection', [
                'type' => 'sylius_checkout_shipment',
                'label' => false,
                'options' => [
                    'criteria' => [
                        'enabled' => true,
                        'zone' => [1, 4],
                    ],
                ],
            ])
            ->shouldBeCalled()
        ;

        $this->preSetData($event);
    }

    function it_adds_field_with_null_as_zone_options_if_no_zones_has_been_matched(
        AddressInterface $address,
        FormEvent $event,
        FormInterface $form,
        OrderInterface $order,
        ZoneMatcherInterface $zoneMatcher
    ) {
        $event->getData()->willReturn($order);
        $event->getForm()->willReturn($form);

        $order->getShippingAddress()->willReturn($address);
        $zoneMatcher->matchAll($address)->willReturn([]);

        $form
            ->add('shipments', 'collection', [
                'type' => 'sylius_checkout_shipment',
                'label' => false,
                'options' => [
                    'criteria' => [
                        'enabled' => true,
                        'zone' => null,
                    ],
                ],
            ])
            ->shouldBeCalled()
        ;

        $this->preSetData($event);
    }
}
