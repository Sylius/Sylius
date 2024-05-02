<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
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
use Symfony\Component\Security\Core\User\LegacyPasswordAuthenticatedUserInterface;

trigger_deprecation('sylius/user-bundle', '1.12', 'The "%s" class is deprecated, use "%s" instead.', UserPasswordEncoder::class, UserPasswordHasher::class);

/**
 * @deprecated, use {@link UserPasswordHasher} instead.
 */
class UserPasswordEncoder implements UserPasswordEncoderInterface
{
    public function __construct(private EncoderFactoryInterface|PasswordHasherFactoryInterface $encoderOrPasswordHasherFactory)
    {
        if ($this->encoderOrPasswordHasherFactory instanceof EncoderFactoryInterface) {
            return;
        }

        throw new \InvalidArgumentException(
            sprintf(
                'Using the "%s" class with "%s" argument is prohibited, use "%s" service instead.',
                self::class,
                PasswordHasherFactoryInterface::class,
                UserPasswordHasher::class,
            ),
        );
    }

    /**
     * @param CredentialsHolderInterface&LegacyPasswordAuthenticatedUserInterface $user
     */
    public function encode(CredentialsHolderInterface $user): string
    {
        $encoder = $this->encoderOrPasswordHasherFactory->getEncoder($user);

        return $encoder->encodePassword($user->getPlainPassword(), $user->getSalt());
    }
}
