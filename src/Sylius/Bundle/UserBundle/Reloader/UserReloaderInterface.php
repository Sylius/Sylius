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

namespace Sylius\Bundle\UserBundle\Reloader;

use Sylius\Component\User\Model\UserInterface;

interface UserReloaderInterface
{
    /**
     * @param UserInterface $user
     */
    public function reloadUser(UserInterface $user): void;
}
