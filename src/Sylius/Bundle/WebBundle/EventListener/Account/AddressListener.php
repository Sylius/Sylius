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
use FOS\UserBundle\Model\UserManagerInterface;

/**
 * Event linked to the creation of an address in the my account section.
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class AddressListener
{
    protected $securityContext;

    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function onAddressPreCreate(ResourceEvent $event)
    {
        // TODO: link address to the user only when it comes from MY ACCOUNT
        $user = $this->securityContext->getToken()->getUser();

        // this will be saved on the flush of the address thanks to
        // the "cascade all" in the relationship User <=> Address
        $user->addAddress($event->getSubject());
    }

    public function onAddressPreDelete(ResourceEvent $event)
    {
        $address = $event->getSubject();
        $user = $this->securityContext->getToken()->getUser();

        if ($address == $user->getBillingAddress()) {
            $user->setBillingAddress(null);
        }
        elseif ($address == $user->getShippingAddress()) {
            $user->setShippingAddress(null);
        }
    }
}