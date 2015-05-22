<?php

/*
 * This file is part of the Lakion package.
 *
 * (c) Lakion
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\Form\EventListener;

use Sylius\Component\User\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class UserRegistrationFormListener implements EventSubscriberInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::SUBMIT => 'submit',
        );
    }

    /**
     * {@inheritDoc}
     */
    public function submit(FormEvent $event)
    {
        $customer = $event->getData();
        if (!$customer instanceof CustomerInterface) {
            throw new UnexpectedTypeException($customer, 'Sylius\Component\User\Model\CustomerInterface');
        }

        if (null !== $user = $customer->getUser()) {
            $user->setEnabled(true);
        }
    }
}
