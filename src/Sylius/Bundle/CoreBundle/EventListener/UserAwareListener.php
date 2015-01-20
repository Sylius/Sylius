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

use Sylius\Component\Cart\Event\CartEvent;
use Sylius\Component\Core\Model\UserAwareInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Security\Core\SecurityContextInterface;

class UserAwareListener
{
    protected $securityContext;

    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function setUser(GenericEvent $event)
    {
        if ($event instanceof CartEvent) {
            $resource = $event->getCart();
        } else {
            $resource = $event->getSubject();
        }

        if (!$resource instanceof UserAwareInterface) {
            throw new UnexpectedTypeException($resource, 'Sylius\Component\Core\Model\UserAwareInterface');
        }

        if (null === $user = $this->getUser()) {
            return;
        }

        $resource->setUser($user);
    }

    protected function getUser()
    {
        if ($this->securityContext->getToken() && $this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->securityContext->getToken()->getUser();
        }
    }
}
