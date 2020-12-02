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
use Sylius\Behat\TestAssertionHelper;
use Sylius\Behat\TestAssertionHelperInterface;
use Webmozart\Assert\Assert;

final class NotificationContext implements Context
{
    /** @var NotificationCheckerInterface */
    private $notificationChecker;

    /** @var TestAssertionHelperInterface */
    private $assertionHelper;

    public function __construct(NotificationCheckerInterface $notificationChecker, TestAssertionHelperInterface $assertionHelper)
    {
        $this->notificationChecker = $notificationChecker;
        $this->assertionHelper = $assertionHelper;
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedItHasBeenSuccessfullyCreated()
    {
        $this->assertionHelper->waitUntilAssertionPasses(
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
        $this->assertionHelper->waitUntilAssertionPasses(
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
        $this->assertionHelper->waitUntilAssertionPasses(
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
        $this->assertionHelper->waitUntilAssertionPasses(
            3,
            function (): void {
                $this->notificationChecker->checkNotification('have been successfully deleted.', NotificationType::success());
            }
        );
    }
}
