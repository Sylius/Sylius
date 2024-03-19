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

namespace Sylius\Component\User\Security;

use Sylius\Component\User\Model\CredentialsHolderInterface;

trigger_deprecation('sylius/user', '1.12', 'The "%s" class is deprecated, use "%s" instead.', UserPasswordEncoderInterface::class, UserPasswordHasherInterface::class);

/**
 * @deprecated since Sylius 1.12, use {@link UserPasswordHasherInterface} instead.
 */
interface UserPasswordEncoderInterface
{
    public function encode(CredentialsHolderInterface $user): string;
}
