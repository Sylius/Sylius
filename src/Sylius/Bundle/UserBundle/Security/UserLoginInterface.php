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

use Sylius\Component\User\Model\UserInterface;

trigger_deprecation(
    'sylius/user-bundle',
    '1.14',
    'The "%s" interface is deprecated and will be removed in Sylius 2.0.',
    UserLoginInterface::class,
);
interface UserLoginInterface
{
    public function login(UserInterface $user, ?string $firewallName = null);
}
