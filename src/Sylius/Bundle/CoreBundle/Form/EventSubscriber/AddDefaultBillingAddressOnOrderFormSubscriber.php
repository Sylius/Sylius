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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class AddDefaultBillingAddressOnOrderFormSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $orderData = $event->getData();

        if ($this->shouldBillingAndShippingAddressBeTheSame($orderData)) {
            $orderData['billingAddress'] = $orderData['shippingAddress'];

            $event->setData($orderData);
        }
    }

    /**
     * @param array $orderData
     *
     * @return bool
     */
    private function shouldBillingAndShippingAddressBeTheSame(array $orderData)
    {
        return
            (!isset($orderData['differentBillingAddress']) || false === $orderData['differentBillingAddress']) &&
            isset($orderData['shippingAddress'])
        ;
    }
}
