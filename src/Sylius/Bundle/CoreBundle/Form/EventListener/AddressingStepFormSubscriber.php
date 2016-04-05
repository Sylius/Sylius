<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\EventListener;

use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Ahmad Rabie <ahmad.rabei.ir@gmail.com>
 */
class AddressingStepFormSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_SET_DATA => 'postSetData',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function postSetData(FormEvent $event)
    {
        /** @var OrderInterface $order */
        $order = $event->getData();

        if (null === $customer = $order->getCustomer()) {
            return;
        }

        $shippingAddress = $order->getShippingAddress();

        if (null !== $shippingAddress && null !== $shippingAddress->getPhoneNumber()) {
            return;
        }

        $event
            ->getForm()
                ->get('shippingAddress')
                ->get('phoneNumber')
            ->setData($customer->getPhoneNumber())
        ;

    }
}
