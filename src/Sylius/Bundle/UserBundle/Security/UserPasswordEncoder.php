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
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UserPasswordEncoder implements UserPasswordEncoderInterface
{
    private EncoderFactoryInterface $encoderFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    public function encode(CredentialsHolderInterface $user): string
    {
        /** @psalm-suppress InvalidArgument */
        $encoder = $this->encoderFactory->getEncoder($user);

        return $encoder->encodePassword($user->getPlainPassword(), $user->getSalt());
    }
}
