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
use Sylius\Component\User\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Sets customer in context from a customer-aware Event subject.
 *
 * @author Aram Alipoor <aram.alipoor@gmail.com>
 */
class CustomerContextListener
{
    /**
     * @var CustomerContextInterface
     */
    protected $customerContext;

    /**
     * @param CustomerContextInterface $customerContext
     */
    public function __construct(CustomerContextInterface $customerContext)
    {
        $this->customerContext = $customerContext;
    }

    /**
     * @param GenericEvent $event
     */
    public function setCustomerContextFromSubject(GenericEvent $event)
    {
        $subject = $event->getSubject();

        if (!$subject instanceof CustomerAwareInterface) {
            throw new UnexpectedTypeException(
                $subject,
                'Sylius\Component\User\Model\CustomerAwareInterface'
            );
        }

        /** @var CustomerInterface $customer */
        if (null === $customer = $subject->getCustomer()) {
            return;
        }

        $this->customerContext->setCustomer($customer);
    }
}
