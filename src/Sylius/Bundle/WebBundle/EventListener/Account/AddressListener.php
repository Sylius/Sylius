<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\EventListener\Account;

use Sylius\Bundle\ResourceBundle\Event\ResourceEvent;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Event linked to the creation of an address in the my account section.
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class AddressListener
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
     * @param ResourceEvent $event
     */
    public function onAddressPreDelete(ResourceEvent $event)
    {
        $address = $event->getSubject();
        $user = $this->securityContext->getToken()->getUser();

        if ($address == $user->getBillingAddress()) {
            $user->setBillingAddress(null);
        }
        if ($address == $user->getShippingAddress()) {
            $user->setShippingAddress(null);
        }
    }
}
