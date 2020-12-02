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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\JavaScriptTestHelper;
use Sylius\Behat\JavaScriptTestHelperInterface;
use Webmozart\Assert\Assert;

final class NotificationContext implements Context
{
    /** @var NotificationCheckerInterface */
    private $notificationChecker;

    /** @var JavaScriptTestHelperInterface */
    private $testHelper;

    public function __construct(NotificationCheckerInterface $notificationChecker, JavaScriptTestHelperInterface $testHelper)
    {
        $this->notificationChecker = $notificationChecker;
        $this->testHelper = $testHelper;
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedItHasBeenSuccessfullyCreated()
    {
        $this->testHelper->waitUntilNotificationPopups(
            3,
            function (): void {
                $this->notificationChecker->checkNotification('has been successfully created.', NotificationType::success());
            }
        );
    }

    /**
     * @Then I should be notified that it has been successfully edited
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyEdited()
    {
        $this->testHelper->waitUntilNotificationPopups(
            3,
            function (): void {
                $this->notificationChecker->checkNotification('has been successfully updated.', NotificationType::success());
            }
        );
    }

    /**
     * @Then I should be notified that it has been successfully deleted
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyDeleted()
    {
        $this->testHelper->waitUntilNotificationPopups(
            3,
            function (): void {
                $this->notificationChecker->checkNotification('has been successfully deleted.', NotificationType::success());
            }
        );
    }

    /**
     * @Then I should be notified that they have been successfully deleted
     */
    public function iShouldBeNotifiedThatTheyHaveBeenSuccessfullyDeleted()
    {
        $this->testHelper->waitUntilNotificationPopups(
            3,
            function (): void {
                $this->notificationChecker->checkNotification('have been successfully deleted.', NotificationType::success());
            }
        );
    }
}
