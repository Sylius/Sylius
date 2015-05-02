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

use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class UserRegistrationFormListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::SUBMIT => 'submit',
        );
    }
    public function submit(FormEvent $event)
    {
        $user = $event->getData();
        if (!$user instanceof UserInterface) {
            throw new UnexpectedTypeException($user, 'Sylius\Component\User\Model\UserInterface');
        }

        $user->setEnabled(true);
    }
}