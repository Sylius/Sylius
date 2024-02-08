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

use Doctrine\Persistence\ObjectManager;
use Sylius\Component\User\Model\UserInterface;

final class UserReloader implements UserReloaderInterface
{
    public function __construct(private ObjectManager $objectManager)
    {
    }

    public function reloadUser(UserInterface $user): void
    {
        $this->objectManager->refresh($user);
    }
}
