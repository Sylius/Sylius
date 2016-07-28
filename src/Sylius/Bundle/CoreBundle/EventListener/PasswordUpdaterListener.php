<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Sylius\Bundle\UserBundle\EventListener\PasswordUpdaterListener as BasePasswordUpdaterListener;

/**
 * User update listener.
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class PasswordUpdaterListener extends BasePasswordUpdaterListener
{
    /**
     * @param GenericEvent $event
     */
    public function customerUpdateEvent(GenericEvent $event)
    {
        $customer = $event->getSubject();

        if (!$customer instanceof CustomerInterface) {
            throw new UnexpectedTypeException(
                $customer,
                'Sylius\Component\Core\Model\CustomerInterface'
            );
        }

        if (null !== $user = $customer->getUser()) {
            $this->updateUserPassword($user);
        }
    }

}
