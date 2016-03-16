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

use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\User\Context\CustomerContextInterface;
use Sylius\Component\User\Model\CustomerAwareInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

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
     * @param GenericEvent $event
     */
    public function setCustomer(GenericEvent $event)
    {
        $resource = $event->getSubject();

        if (!$resource instanceof CustomerAwareInterface) {
            throw new UnexpectedTypeException($resource, CustomerAwareInterface::class);
        }

        if (null === $customer = $this->customerContext->getCustomer()) {
            return;
        }

        $resource->setCustomer($customer);
    }
}
