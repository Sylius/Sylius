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
use Sylius\Component\User\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Security\Core\Exception\AccountStatusException;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class UserLoginListener
{
    /**
     * @var UserLoginInterface
     */
    protected $userLogin;

    public function __construct(UserLoginInterface $userLogin)
    {
        $this->userLogin = $userLogin;
    }

    public function login(GenericEvent $event)
    {
        $customer = $event->getSubject();

        if (!$customer instanceof CustomerInterface) {
            throw new UnexpectedTypeException(
                $customer,
                'Sylius\Component\User\Model\CustomerInterface'
            );
        }

        if (null === $user = $customer->getUser()) {
            return;
        }

        try {
            $this->userLogin->login($user);
        } catch (AccountStatusException $exception) {
            // We simply do not authenticate users which do not pass the user
            // checker (not enabled, expired, etc.).
        }
    }
}
