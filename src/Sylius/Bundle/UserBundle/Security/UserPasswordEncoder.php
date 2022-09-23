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

namespace Sylius\Bundle\UserBundle\Security;

use Sylius\Component\User\Model\CredentialsHolderInterface;
use Sylius\Component\User\Security\UserPasswordEncoderInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

trigger_deprecation('sylius/user-bundle', '1.12', 'The "%s" class is deprecated, use "%s" instead.', UserPasswordEncoder::class, UserPasswordHasher::class);

class UserPasswordEncoder implements UserPasswordEncoderInterface
{
    public function __construct(private EncoderFactoryInterface|PasswordHasherFactoryInterface $encoderFactory)
    {
    }

    public function encode(CredentialsHolderInterface $user): string
    {
        if ($this->encoderFactory instanceof PasswordHasherFactoryInterface) {
            /** @psalm-suppress InvalidArgument */
            $passwordHasher = $this->encoderFactory->getPasswordHasher($user::class);

            return $passwordHasher->hash($user->getPlainPassword(), $user->getSalt());
        }

        /** @psalm-suppress InvalidArgument */
        $encoder = $this->encoderFactory->getEncoder($user);

        return $encoder->encodePassword($user->getPlainPassword(), $user->getSalt());
    }
}
