<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Repository;

interface WishlistRepositoryInterface
{
    /**
     * Gets emails for specific notification type.
     *
     * @param int $notifyOn
     *
     * @return string[]
     */
    public function findEmailsByNotification($notifyOn);
}
