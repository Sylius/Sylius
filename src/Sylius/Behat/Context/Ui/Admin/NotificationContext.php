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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\Helper\JavaScriptTestHelperInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;

final class NotificationContext implements Context
{
    public function __construct(
        private NotificationCheckerInterface $notificationChecker,
        private JavaScriptTestHelperInterface $testHelper,
    ) {
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedItHasBeenSuccessfullyCreated(): void
    {
        $this->testHelper->waitUntilNotificationPopups(
            $this->notificationChecker,
            NotificationType::success(),
            'has been successfully created.',
        );
    }

    /**
     * @Then I should be notified that it has been successfully edited
     * @Then I should be notified that it has been successfully uploaded
     * @Then I should be notified that the changes have been successfully applied
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyEdited(): void
    {
        $this->testHelper->waitUntilNotificationPopups(
            $this->notificationChecker,
            NotificationType::success(),
            'has been successfully updated.',
        );
    }

    /**
     * @Then I should be notified that it :has been successfully deleted
     * @Then I should be notified that they :have been successfully deleted
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyDeleted(string $hasHave): void
    {
        $this->testHelper->waitUntilNotificationPopups(
            $this->notificationChecker,
            NotificationType::success(),
            sprintf('%s been successfully deleted.', $hasHave),
        );
    }

    /**
     * @Then I should be notified that the removal operation has started successfully
     */
    public function iShouldBeNotifiedThatTheRemovalOperationHasStartedSuccessfully(): void
    {
        $this->testHelper->waitUntilNotificationPopups(
            $this->notificationChecker,
            NotificationType::success(),
            'has been requested. This process can take a while depending on the number of affected products.',
        );
    }

    /**
     * @Then I should be notified that it is in use
     */
    public function iShouldBeNotifiedThatItIsInUse(): void
    {
        $this->testHelper->waitUntilNotificationPopups(
            $this->notificationChecker,
            NotificationType::failure(),
            'Cannot delete',
        );
    }
}
