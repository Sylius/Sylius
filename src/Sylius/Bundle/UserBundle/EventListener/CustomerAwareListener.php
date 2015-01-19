<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\EventListener;

use Sylius\Component\Cart\Event\CartEvent;
use Sylius\Component\Resource\Event\ResourceEvent;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\User\Context\CustomerContextInterface;
use Sylius\Component\User\Model\CustomerAwareInterface;

class CustomerAwareListener
{
    /**
     * @var CustomerContextInterface
     */
    protected $customerContext;

    /**
     * @param CustomerContextInterface $securityContext
     */
    public function __construct(CustomerContextInterface $securityContext)
    {
        $this->customerContext = $securityContext;
    }

    /**
     * @param ResourceEvent|CartEvent $event
     */
    public function setCustomer($event)
    {
        if ($event instanceof CartEvent) {
            $resource = $event->getCart();
        } elseif ($event instanceof ResourceEvent) {
            $resource = $event->getResource();
        } else {
            throw new \InvalidArgumentException('CustomerAwareListener expects CartEvent or ResourceEvent.');
        }

        if (!$resource instanceof CustomerAwareInterface) {
            throw new UnexpectedTypeException($resource, 'Sylius\Component\User\Model\CustomerAwareInterface');
        }

        if (null === $customer = $this->customerContext->getCustomer()) {
            return;
        }

        $resource->setCustomer($customer);
    }
}
