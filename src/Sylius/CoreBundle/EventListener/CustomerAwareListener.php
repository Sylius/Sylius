<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\CoreBundle\EventListener;

use Sylius\UserBundle\EventListener\CustomerAwareListener as BaseCustomerAwareListener;
use Sylius\Cart\Event\CartEvent;
use Sylius\Resource\Exception\UnexpectedTypeException;
use Sylius\User\Model\CustomerAwareInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class CustomerAwareListener extends BaseCustomerAwareListener
{
    /**
     * @param GenericEvent $event
     */
    public function setCustomer(GenericEvent $event)
    {
        if ($event instanceof CartEvent) {
            $resource = $event->getCart();
        } else {
            $resource = $event->getSubject();
        }

        if (!$resource instanceof CustomerAwareInterface) {
            throw new UnexpectedTypeException($resource, CustomerAwareInterface::class);
        }

        if (null === $customer = $this->customerContext->getCustomer()) {
            return;
        }

        $resource->setCustomer($customer);
    }
}
