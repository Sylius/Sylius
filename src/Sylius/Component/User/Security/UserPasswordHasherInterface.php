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

trigger_deprecation(
    'sylius/user-bundle',
    '1.14',
    'The "%s" interface is deprecated and will be removed in Sylius 2.0.',
    UserPasswordHasherInterface::class,
);
interface UserPasswordHasherInterface
{
    public function hash(CredentialsHolderInterface $user): string;
}
