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
use Sylius\Behat\Service\Helper\JavaScriptTestHelperInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;

final class NotificationContext implements Context
{
    private NotificationCheckerInterface $notificationChecker;

    private JavaScriptTestHelperInterface $testHelper;

    public function __construct(NotificationCheckerInterface $notificationChecker, JavaScriptTestHelperInterface $testHelper)
    {
        $this->notificationChecker = $notificationChecker;
        $this->testHelper = $testHelper;
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedItHasBeenSuccessfullyCreated(): void
    {
        $this->testHelper->waitUntilNotificationPopups(
            $this->notificationChecker,
            NotificationType::success(),
            'has been successfully created.'
        );
    }

    /**
     * @Then I should be notified that it has been successfully edited
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyEdited(): void
    {
        $this->testHelper->waitUntilNotificationPopups(
            $this->notificationChecker,
            NotificationType::success(),
            'has been successfully updated.'
        );
    }

    /**
     * @Then I should be notified that it has been successfully deleted
     * @Then I should be notified that they have been successfully deleted
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyDeleted(): void
    {
        $this->testHelper->waitUntilNotificationPopups(
            $this->notificationChecker,
            NotificationType::success(),
            'has been successfully deleted.'
        );
    }
}
