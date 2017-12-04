<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\User\Security;

use Sylius\Component\User\Model\CredentialsHolderInterface;

final class PasswordUpdater implements PasswordUpdaterInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    /**
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->userPasswordEncoder = $passwordEncoder;
    }

    /**
     * {@inheritdoc}
     */
    public function updatePassword(CredentialsHolderInterface $user): void
    {
        if ('' !== $password = $user->getPlainPassword()) {
            $user->setPassword($this->userPasswordEncoder->encode($user));
            $user->eraseCredentials();
        }
    }
}
