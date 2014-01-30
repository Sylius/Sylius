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

use Sylius\Bundle\CartBundle\Event\CartEvent;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\CoreBundle\Model\UserInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Security\Core\SecurityContextInterface;

class OrderUserListener
{
    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * @param GenericEvent $event
     *
     * @throws \InvalidArgumentException
     */
    public function setOrderUser(GenericEvent $event)
    {
        if ($event instanceof CartEvent) {
            $order = $event->getCart();
        } else {
            $order = $event->getSubject();
        }

        if (!$order instanceof OrderInterface) {
            throw new \InvalidArgumentException(sprintf(
                'Order user listener requires event subject to be instance of "Sylius\Bundle\CoreBundle\Model\OrderInterface", "%s" given.',
                is_object($order) ? get_class($order) : gettype($order)
            ));
        }

        if (null === $user = $this->getUser()) {
            return;
        }

        $order->setUser($user);
    }

    /**
     * @return UserInterface|null
     */
    protected function getUser()
    {
        if ($this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->securityContext->getToken()->getUser();
        }

        return null;
    }
}
