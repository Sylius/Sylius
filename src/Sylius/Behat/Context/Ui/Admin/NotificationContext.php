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
use Sylius\Behat\Element\Admin\NotificationsElementInterface;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Exception\ResourceDeleteException;
use Webmozart\Assert\Assert;

final readonly class NotificationContext implements Context
{
    public function __construct(
        private NotificationsElementInterface $notificationsElement,
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedItHasBeenSuccessfullyCreated(): void
    {
        Assert::true($this->notificationsElement->hasNotification((string) NotificationType::success(), 'has been successfully created.'));
    }

    /**
     * @Then I should be notified that it has been successfully edited
     * @Then I should be notified that it has been successfully uploaded
     * @Then I should be notified that the changes have been successfully applied
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyEdited(): void
    {
        Assert::true($this->notificationsElement->hasNotification((string) NotificationType::success(), 'has been successfully updated.'));
    }

    /**
     * @Then I should be notified that it :has been successfully deleted
     * @Then I should be notified that they :have been successfully deleted
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyDeleted(string $hasHave): void
    {
        Assert::true(
            $this->notificationsElement->hasNotification(
                (string) NotificationType::success(),
                sprintf('%s been successfully deleted.', $hasHave),
            ),
        );
    }

    /**
     * @Then I should be notified that the removal operation has started successfully
     */
    public function iShouldBeNotifiedThatTheRemovalOperationHasStartedSuccessfully(): void
    {
        Assert::true(
            $this->notificationsElement->hasNotification(
                (string) NotificationType::success(),
                'has been requested. This process can take a while depending on the number of affected products.',
            ),
        );
    }

    /**
     * @Then I should be notified that it is in use
     */
    public function iShouldBeNotifiedThatItIsInUse(): void
    {
        if ($this->sharedStorage->has('last_exception')) {
            /** @var ResourceDeleteException $exception */
            $exception = $this->sharedStorage->get('last_exception');

            Assert::contains($exception->getMessage(), 'Cannot delete');

            return;
        }

        Assert::true($this->notificationsElement->hasNotification((string) NotificationType::error(), 'Cannot delete'));
    }
}
