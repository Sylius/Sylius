<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\Form\EventSubscriber;

use Sylius\Component\User\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class UserRegistrationFormSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::SUBMIT => 'submit',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function submit(FormEvent $event)
    {
        $customer = $event->getData();
        if (!$customer instanceof CustomerInterface) {
            throw new UnexpectedTypeException($customer, CustomerInterface::class);
        }

        if (null !== $user = $customer->getUser()) {
            $user->setEnabled(true);
        }
    }
}
