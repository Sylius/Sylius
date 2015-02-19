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

use Sylius\Bundle\UserBundle\Security\UserLoginInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * User register listener.
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UserRegisterListener
{
    /**
     * @var UserLoginInterface
     */
    protected $userLogin;

    /**
     * @var PasswordUpdaterInterface
     */
    protected $passwordUpdater;

    public function __construct(PasswordUpdaterInterface $passwordUpdater, UserLoginInterface $userLogin)
    {
        $this->passwordUpdater = $passwordUpdater;
        $this->userLogin = $userLogin;
    }

    public function preRegistration(GenericEvent $event)
    {
        $user = $event->getSubject();

        if (!$user instanceof UserInterface) {
            throw new UnexpectedTypeException(
                $user,
                'Sylius\Component\User\Model\UserInterface'
            );
        }

        $this->passwordUpdater->updatePassword($user);
    }

    public function postRegistration(GenericEvent $event)
    {
        $user = $event->getSubject();

        if (!$user instanceof UserInterface) {
            throw new UnexpectedTypeException(
                $user,
                'Sylius\Component\User\Model\UserInterface'
            );
        }

        $this->userLogin->login($user);
    }
}
