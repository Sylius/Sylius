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

namespace Sylius\Behat\Service\Helper;

use FriendsOfBehat\PageObjectExtension\Page\PageInterface;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\NotificationCheckerInterface;

interface JavaScriptTestHelperInterface
{
    public function waitUntilAssertionPasses(callable $assertion, ?int $timeout = null): void;

    public function waitUntilNotificationPopups(NotificationCheckerInterface $notificationChecker, NotificationType $type, string $message, ?int $timeout = null): void;

    public function waitUntilPageOpens(PageInterface $page, ?array $options = [], ?int $timeout = null): void;
}
