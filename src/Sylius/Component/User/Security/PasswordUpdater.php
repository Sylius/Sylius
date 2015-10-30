<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\User\Security;

use Sylius\Component\User\Model\CredentialingInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class PasswordUpdater implements PasswordUpdaterInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->userPasswordEncoder = $passwordEncoder;
    }

    /**
     * {@inheritDoc}
     */
    public function updatePassword(CredentialingInterface $user)
    {
        if (0 !== strlen($password = $user->getPlainPassword())) {
            $user->setPassword($this->userPasswordEncoder->encode($user));
            $user->eraseCredentials();
        }
    }
}
