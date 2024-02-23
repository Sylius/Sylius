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

use Behat\Mink\Exception\ElementNotFoundException;
use FriendsOfBehat\PageObjectExtension\Page\PageInterface;
use FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException;
use Sylius\Behat\Exception\NotificationExpectationMismatchException;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\NotificationCheckerInterface;

final class JavaScriptTestHelper implements JavaScriptTestHelperInterface
{
    public function __construct(
        private int $microsecondsInterval,
        private int $defaultTimeout,
    ) {
    }

    public function waitUntilAssertionPasses(callable $callable, ?int $timeout = null): void
    {
        $this->waitUntilExceptionDisappears($callable, \InvalidArgumentException::class, $timeout);
    }

    public function waitUntilNotificationPopups(
        NotificationCheckerInterface $notificationChecker,
        NotificationType $type,
        string $message,
        ?int $timeout = null,
    ): void {
        $callable = function () use ($notificationChecker, $message, $type): void {
            $notificationChecker->checkNotification($message, $type);
        };

        $this->waitUntilExceptionDisappears(
            $callable,
            ElementNotFoundException::class,
            $timeout,
            $type,
        );
    }

    public function waitUntilPageOpens(PageInterface $page, ?array $options = [], ?int $timeout = null): void
    {
        $callable = function () use ($page, $options): void {
            $page->open($options);
        };

        $this->waitUntilExceptionDisappears($callable, UnexpectedPageException::class, $timeout);
    }

    private function waitUntilExceptionDisappears(
        callable $callable,
        string $exceptionClass,
        ?int $timeout = null,
        ?NotificationType $type = null,
    ): void {
        $start = microtime(true);
        $timeout ??= $this->defaultTimeout;
        $end = $start + $timeout;

        do {
            try {
                $callable();
            } catch (NotificationExpectationMismatchException $exception) {
                throw new NotificationExpectationMismatchException($type, $exception->getMessage());
            } catch (\Exception $exception) {
                if ($exception instanceof $exceptionClass) {
                    usleep($this->microsecondsInterval);

                    continue;
                }
            }

            return;
        } while (microtime(true) < $end);

        throw new \InvalidArgumentException('Time has run out and the assertion has not passed yet.');
    }
}
