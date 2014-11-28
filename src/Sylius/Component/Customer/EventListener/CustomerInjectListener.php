<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Customer\EventListener;

use Sylius\Component\Customer\Model\CustomerAwareInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Security\Core\SecurityContextInterface;

class CustomerInjectListener
{
    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * @var string
     */
    protected $interface;

    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * @param string $interface
     */
    public function setInterfaceName($interface)
    {
        $this->interface = $interface;
    }

    /**
     * @param GenericEvent $event
     */
    public function setCustomer(GenericEvent $event)
    {
        if (null === $object = $event->getSubject()) {
            return;
        }

        if (!$this->supports($object)) {
            throw new UnexpectedTypeException($object, $this->interface);
        }

        if (null !== $object->getCustomer()) {
            return;
        }

        if (null === $customer = $this->getCustomer()) {
            return;
        }

        if ($customer instanceof CustomerAwareInterface) {
            $customer = $customer->getCustomer();
        }

        $object->setCustomer($customer);
    }

    /**
     * @param object $object
     *
     * @return bool
     */
    protected function supports($object)
    {
        return $object instanceof $this->interface || in_array($this->interface, class_implements($object));
    }

    /**
     * @return null|CustomerInterface|CustomerAwareInterface
     */
    protected function getCustomer()
    {
        if ($this->securityContext->isGranted('IS_CUSTOMER') || $this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->securityContext->getToken()->getUser();
        }
    }
}
