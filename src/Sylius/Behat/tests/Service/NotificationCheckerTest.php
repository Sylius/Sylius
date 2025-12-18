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

namespace Tests\Sylius\Behat\Service;

use Behat\Mink\Element\NodeElement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Behat\Exception\NotificationExpectationMismatchException;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\Accessor\NotificationAccessorInterface;
use Sylius\Behat\Service\NotificationChecker;
use Sylius\Behat\Service\NotificationCheckerInterface;

final class NotificationCheckerTest extends TestCase
{
    private MockObject&NotificationAccessorInterface $notificationAccessor;

    private NotificationChecker $notificationChecker;

    protected function setUp(): void
    {
        $this->notificationAccessor = $this->createMock(NotificationAccessorInterface::class);

        $this->notificationChecker = new NotificationChecker($this->notificationAccessor, ['failure' => 'negative', 'success' => 'alert-success']);
    }

    public function testImplementsNotificationCheckerInterface(): void
    {
        $this->assertInstanceOf(NotificationCheckerInterface::class, $this->notificationChecker);
    }

    public function testChecksIfSuccessfulNotificationWithSpecificMessageHasAppeared(): void
    {
        /** @var NodeElement&MockObject $firstMessage */
        $firstMessage = $this->createMock(NodeElement::class);
        /** @var NodeElement&MockObject $secondMessage */
        $secondMessage = $this->createMock(NodeElement::class);

        $this->notificationAccessor->expects($this->once())->method('getMessageElements')->willReturn([$firstMessage, $secondMessage]);
        $firstMessage->expects($this->once())->method('getText')->willReturn('Some resource has been successfully edited.');
        $firstMessage->expects($this->never())->method('hasClass');
        $secondMessage->expects($this->once())->method('getText')->willReturn('Some resource has been successfully deleted.');
        $secondMessage->expects($this->once())->method('hasClass')->with('alert-success')->willReturn(true);

        $this->notificationChecker->checkNotification('Some resource has been successfully deleted.', NotificationType::success());
    }

    public function testChecksIfFailureNotificationWithSpecificMessageHasAppeared(): void
    {
        /** @var NodeElement&MockObject $firstMessage */
        $firstMessage = $this->createMock(NodeElement::class);
        /** @var NodeElement&MockObject $secondMessage */
        $secondMessage = $this->createMock(NodeElement::class);

        $this->notificationAccessor->expects($this->once())->method('getMessageElements')->willReturn([$firstMessage, $secondMessage]);
        $firstMessage->expects($this->once())->method('getText')->willReturn('Some resource has been successfully edited.');
        $firstMessage->expects($this->never())->method('hasClass');
        $secondMessage->expects($this->once())->method('getText')->willReturn('Some resource could not be deleted.');
        $secondMessage->expects($this->once())->method('hasClass')->with('negative')->willReturn(true);

        $this->notificationChecker->checkNotification('Some resource could not be deleted.', NotificationType::failure());
    }

    public function testThrowsExceptionIfNoMessageWithGivenContentAndTypeHasBeenFound(): void
    {
        /** @var NodeElement&MockObject $firstMessage */
        $firstMessage = $this->createMock(NodeElement::class);
        /** @var NodeElement&MockObject $secondMessage */
        $secondMessage = $this->createMock(NodeElement::class);

        $this->notificationAccessor->expects($this->once())->method('getMessageElements')->willReturn([$firstMessage, $secondMessage]);
        $firstMessage->expects($this->once())->method('getText')->willReturn('Some resource has been successfully edited.');
        $firstMessage->expects($this->never())->method('hasClass');
        $secondMessage->expects($this->once())->method('getText')->willReturn('Some resource could not be deleted.');
        $secondMessage->expects($this->never())->method('hasClass');
        $this->expectException(NotificationExpectationMismatchException::class);

        $this->notificationChecker->checkNotification('Some resource has been successfully created.', NotificationType::failure());
    }
}
