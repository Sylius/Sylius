<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Service;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface NotificationCheckerInterface
{
    /**
     * @param string $resource
     */
    public function checkDeletionNotification($resource);

    /**
     * @param string $resource
     */
    public function checkCreationNotification($resource);

    /**
     * @param string $resource
     */
    public function checkEditionNotification($resource);

    /**
     * @param string $message
     */
    public function checkSuccessNotificationMessage($message);

    /**
     * @param string $message
     */
    public function checkFailureNotificationMessage($message);
}
