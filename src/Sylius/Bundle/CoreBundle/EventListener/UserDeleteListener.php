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

use FOS\UserBundle\Model\UserInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Event\ResourceEvent;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * User delete listener.
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class UserDeleteListener
{
    protected $securityContext;
    protected $session;
    protected $router;
    protected $redirectTo;

    public function __construct(SecurityContext $securityContext, UrlGeneratorInterface $router, SessionInterface $session, $redirectTo)
    {
        $this->securityContext = $securityContext;
        $this->session = $session;
        $this->router = $router;
        $this->redirectTo = $redirectTo;
    }

    public function deleteUser(ResourceEvent $event)
    {
        $user = $event->getSubject();

        if (!$user instanceof UserInterface) {
            throw new UnexpectedTypeException(
                $user,
                'FOS\UserBundle\Model\UserInterface'
            );
        }

        if ($this->securityContext->getToken()->getUsername() === $user->getUsernameCanonical()) {
            $event->stopPropagation();
            $this->session->getBag('flashes')->add("error", "Cannot remove currently logged user.");
        }
    }
}
