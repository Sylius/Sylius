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

use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Webmozart\Assert\Assert;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class UserDeleteListener
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param SessionInterface $session
     */
    public function __construct(TokenStorageInterface $tokenStorage, SessionInterface $session)
    {
        $this->tokenStorage = $tokenStorage;
        $this->session = $session;
    }

    /**
     * @param GenericEvent $event
     *
     * @throws \InvalidArgumentException
     */
    public function deleteUser(GenericEvent $event)
    {
        $user = $event->getSubject();
        Assert::isInstanceOf($user, UserInterface::class);

        $token = $this->tokenStorage->getToken();

        if ((null !== $token) && ($loggedUser = $token->getUser()) && ($loggedUser->getId() === $user->getId())) {
            $event->stopPropagation();
            $this->session->getBag('flashes')->add('error', 'Cannot remove currently logged in user.');
        }
    }
}
