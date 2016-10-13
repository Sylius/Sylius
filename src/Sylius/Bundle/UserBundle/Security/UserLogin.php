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
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class UserLogin implements UserLoginInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var UserCheckerInterface
     */
    private $userChecker;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param UserCheckerInterface $userChecker
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        UserCheckerInterface $userChecker,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->userChecker = $userChecker;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function login(UserInterface $user, $firewallName = 'main')
    {
        $this->userChecker->checkPreAuth($user);
        $this->userChecker->checkPostAuth($user);

        $token = $this->createToken($user, $firewallName);
        if (!$token->isAuthenticated()) {
            throw new AuthenticationException('Unauthenticated token');
        }

        $this->tokenStorage->setToken($token);
        $this->eventDispatcher->dispatch(UserEvents::SECURITY_IMPLICIT_LOGIN, new UserEvent($user));
    }

    /**
     * @param UserInterface $user
     * @param string $firewallName
     *
     * @return UsernamePasswordToken
     */
    protected function createToken(UserInterface $user, $firewallName)
    {
        return new UsernamePasswordToken($user, null, $firewallName, $user->getRoles());
    }
}
