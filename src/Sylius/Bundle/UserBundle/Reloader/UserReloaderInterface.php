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

namespace Sylius\Bundle\UserBundle\Reloader;

use Sylius\Component\User\Model\UserInterface;

interface UserReloaderInterface
{
    public function reloadUser(UserInterface $user): void;
}
