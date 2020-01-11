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

namespace Sylius\Behat\Service;

use Sylius\Behat\Exception\NotificationExpectationMismatchException;
use Sylius\Behat\NotificationType;

interface NotificationCheckerInterface
{
    /**
     * @throws NotificationExpectationMismatchException
     */
    public function checkNotification(string $message, NotificationType $type): void;
}
