<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\Security;

use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Bundle\UserBundle\UserEvents;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class UserLogin implements UserLoginInterface
{
    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @var UserCheckerInterface
     */
    private $userChecker;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param SecurityContextInterface $securityContext
     * @param UserCheckerInterface     $userChecker
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(SecurityContextInterface $securityContext, UserCheckerInterface $userChecker, EventDispatcherInterface $eventDispatcher)
    {
        $this->securityContext = $securityContext;
        $this->userChecker = $userChecker;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritDoc}
     */
    public function login(UserInterface $user, $firewallName = 'main')
    {
        $this->userChecker->checkPreAuth($user);

        $token = new UsernamePasswordToken($user, null, $firewallName, $user->getRoles());
        $this->securityContext->setToken($token);
        $this->eventDispatcher->dispatch(UserEvents::SECURITY_IMPLICIT_LOGIN, new UserEvent($user));
    }
}
