<?php

declare(strict_types=1);

namespace Sylius\Behat\Service\Helper;

use FriendsOfBehat\PageObjectExtension\Page\PageInterface;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\NotificationCheckerInterface;

interface JavaScriptTestHelperInterface
{
    public function waitUntilAssertionPasses(callable $assertion, ?int $timeout = null): void;

    public function waitUntilNotificationPopups(NotificationCheckerInterface $notificationChecker, NotificationType $type, string $message, ?int $timeout = null): void;

    public function waitUntilPageOpens(PageInterface $page, ?array $options, ?int $timeout = null): void;
}
