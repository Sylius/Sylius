<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Service;

use PhpSpec\ObjectBehavior;
use Sylius\Behat\Exception\NotificationExpectationMismatchException;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\Accessor\NotificationAccessorInterface;
use Sylius\Behat\Service\NotificationChecker;
use Sylius\Behat\Service\NotificationCheckerInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class NotificationCheckerSpec extends ObjectBehavior
{
    function let(NotificationAccessorInterface $notificationAccessor)
    {
        $this->beConstructedWith($notificationAccessor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(NotificationChecker::class);
    }

    function it_implements_notification_checker_interface()
    {
        $this->shouldImplement(NotificationCheckerInterface::class);
    }

    function it_checks_if_successful_notification_has_appeared(NotificationAccessorInterface $notificationAccessor)
    {
        $notificationAccessor->getType()->willReturn(NotificationType::success());
        $notificationAccessor->getMessage()->willReturn('Some resource has been successfully deleted.');

        $this->checkNotification('Some resource has been successfully deleted.', NotificationType::success());
    }

    function it_checks_if_failure_notification_has_appeared(NotificationAccessorInterface $notificationAccessor)
    {
        $notificationAccessor->getType()->willReturn(NotificationType::failure());
        $notificationAccessor->getMessage()->willReturn('Something went wrong.');

        $this->checkNotification('Something went wrong.', NotificationType::failure());
    }

    function it_throws_notification_mismatch_exception_if_different_or_no_success_notification_has_been_found(
        NotificationAccessorInterface $notificationAccessor
    ) {
        $notificationAccessor->getType()->willReturn(NotificationType::success());
        $notificationAccessor->getMessage()->willReturn('Some resource has been successfully updated.');

        $this->shouldThrow(
            new NotificationExpectationMismatchException(
                NotificationType::success(),
                'Some resource has been successfully created.',
                NotificationType::success(),
                'Some resource has been successfully updated.'
            )
        )->during('checkNotification', ['Some resource has been successfully created.', NotificationType::success()]);
    }

    function it_throws_notification_mismatch_exception_if_failure_message_type_has_been_found_but_expect_success(
        NotificationAccessorInterface $notificationAccessor
    ) {
        $notificationAccessor->getType()->willReturn(NotificationType::failure());
        $notificationAccessor->getMessage()->willReturn('Some resource has been successfully created.');

        $this->shouldThrow(
            new NotificationExpectationMismatchException(
                NotificationType::success(),
                'Some resource has been successfully created.',
                NotificationType::failure(),
                'Some resource has been successfully created.'
            )
        )->during('checkNotification', ['Some resource has been successfully created.', NotificationType::success()]);
    }

    function it_throws_notification_mismatch_exception_if_different_or_no_failure_notification_has_been_found(
        NotificationAccessorInterface $notificationAccessor
    ) {
        $notificationAccessor->getType()->willReturn(NotificationType::failure());
        $notificationAccessor->getMessage()->willReturn('Something different went wrong.');

        $this->shouldThrow(
            new NotificationExpectationMismatchException(
                NotificationType::failure(),
                'Something went wrong.',
                NotificationType::failure(),
                'Something different went wrong.'
            )
        )->during('checkNotification', ['Something went wrong.', NotificationType::failure()]);
    }

    function it_throws_notification_mismatch_exception_if_success_message_type_has_been_found_but_expect_failure(
        NotificationAccessorInterface $notificationAccessor
    ) {
        $notificationAccessor->getType()->willReturn(NotificationType::success());
        $notificationAccessor->getMessage()->willReturn('Something went wrong.');

        $this->shouldThrow(
            new NotificationExpectationMismatchException(
                NotificationType::failure(),
                'Something went wrong.',
                NotificationType::success(),
                'Something went wrong.'
            )
        )->during('checkNotification', ['Something went wrong.', NotificationType::failure()]);
    }
}
