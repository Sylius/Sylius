<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\EventSubscriber;

use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class AvailableShippingMethodsSubscriber implements EventSubscriberInterface
{
    /**
     * @var ZoneMatcherInterface
     */
    private $zoneMatcher;

    /**
     * @param ZoneMatcherInterface $zoneMatcher
     */
    public function __construct(ZoneMatcherInterface $zoneMatcher)
    {
        $this->zoneMatcher = $zoneMatcher;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData'
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $form->add('shipments', 'collection', [
            'type' => 'sylius_checkout_shipment',
            'label' => false,
            'options' => [
                'criteria' => [
                    'enabled' => true,
                    'zone' => $this->getZonesForOrder($data),
                ]
            ],
        ]);
    }

    /**
     * @param OrderInterface $order
     *
     * @return array|null
     */
    private function getZonesForOrder(OrderInterface $order)
    {
        $zones = $this->zoneMatcher->matchAll($order->getShippingAddress());
        if (empty($zones)) {
            return null;
        }

        return array_map(function ($zone) { return $zone->getId(); }, $zones);
    }
}
