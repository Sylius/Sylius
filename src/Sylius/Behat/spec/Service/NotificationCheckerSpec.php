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
use Sylius\Behat\Service\Accessor\NotificationAccessorInterface;
use Sylius\Behat\Service\NotificationChecker;
use Sylius\Behat\Service\NotificationCheckerInterface;

/**
 * @mixin NotificationChecker
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class NotificationCheckerSpec extends ObjectBehavior
{
    function let(NotificationAccessorInterface $notificationAccessor)
    {
        $this->beConstructedWith($notificationAccessor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Service\NotificationChecker');
    }

    function it_implements_notification_checker_interface()
    {
        $this->shouldImplement(NotificationCheckerInterface::class);
    }

    function it_checks_if_successful_creation_notification_has_appeared(NotificationAccessorInterface $notificationAccessor)
    {
        $notificationAccessor->hasSuccessMessage()->willReturn(true);
        $notificationAccessor->hasMessage('Some resource has been successfully created.')->willReturn(true);

        $this->checkCreationNotification('some_resource');
    }

    function it_checks_if_successful_edition_notification_has_appeared(NotificationAccessorInterface $notificationAccessor)
    {
        $notificationAccessor->hasSuccessMessage()->willReturn(true);
        $notificationAccessor->hasMessage('Some resource has been successfully updated.')->willReturn(true);

        $this->checkEditionNotification('some_resource');
    }

    function it_checks_if_successful_deletion_notification_has_appeared(NotificationAccessorInterface $notificationAccessor)
    {
        $notificationAccessor->hasSuccessMessage()->willReturn(true);
        $notificationAccessor->hasMessage('Some resource has been successfully deleted.')->willReturn(true);

        $this->checkDeletionNotification('some_resource');
    }

    function it_checks_if_successful_notification_has_appeared(NotificationAccessorInterface $notificationAccessor)
    {
        $notificationAccessor->hasSuccessMessage()->willReturn(true);
        $notificationAccessor->hasMessage('Some resource has been successfully deleted.')->willReturn(true);

        $this->checkSuccessNotificationMessage('Some resource has been successfully deleted.');
    }

    function it_checks_if_failure_notification_has_appeared(NotificationAccessorInterface $notificationAccessor)
    {
        $notificationAccessor->hasFailureMessage()->willReturn(true);
        $notificationAccessor->hasMessage('Something went wrong.')->willReturn(true);

        $this->checkFailureNotificationMessage('Something went wrong.');
    }

    function it_throws_notification_mismatch_exception_if_different_or_no_success_notification_has_been_found(
        NotificationAccessorInterface $notificationAccessor
    ) {
        $notificationAccessor->hasSuccessMessage()->willReturn(true);
        $notificationAccessor->hasMessage('Some resource has been successfully created.')->willReturn(false);
        $notificationAccessor->getMessageType()->willReturn('success');
        $notificationAccessor->getMessage()->willReturn('Some resource has been successfully updated.');

        $this->shouldThrow(
            new NotificationExpectationMismatchException(
                'success', 
                'Some resource has been successfully created.',
                'success', 
                'Some resource has been successfully updated.'
            )
        )->during('checkSuccessNotificationMessage', ['Some resource has been successfully created.']);
    }

    function it_throws_notification_mismatch_exception_if_failure_message_type_has_been_found_but_expect_success(
        NotificationAccessorInterface $notificationAccessor
    ) {
        $notificationAccessor->hasSuccessMessage()->willReturn(false);
        $notificationAccessor->hasMessage('Some resource has been successfully created.')->willReturn(false);
        $notificationAccessor->getMessageType()->willReturn('failure');
        $notificationAccessor->getMessage()->willReturn('Some resource has been successfully created.');

        $this->shouldThrow(
            new NotificationExpectationMismatchException(
                'success',
                'Some resource has been successfully created.',
                'failure',
                'Some resource has been successfully created.'
            )
        )->during('checkSuccessNotificationMessage', ['Some resource has been successfully created.']);
    }

    function it_throws_notification_mismatch_exception_if_different_or_no_failure_notification_has_been_found(
        NotificationAccessorInterface $notificationAccessor
    ) {
        $notificationAccessor->hasFailureMessage()->willReturn(true);
        $notificationAccessor->hasMessage('Something went wrong.')->willReturn(false);
        $notificationAccessor->getMessageType()->willReturn('failure');
        $notificationAccessor->getMessage()->willReturn('Something different went wrong.');

        $this->shouldThrow(
            new NotificationExpectationMismatchException(
                'failure',
                'Something went wrong.',
                'failure',
                'Something different went wrong.'
            )
        )->during('checkFailureNotificationMessage', ['Something went wrong.']);
    }

    function it_throws_notification_mismatch_exception_if_success_message_type_has_been_found_but_expect_failure(
        NotificationAccessorInterface $notificationAccessor
    ) {
        $notificationAccessor->hasFailureMessage()->willReturn(false);
        $notificationAccessor->getMessageType()->willReturn('success');
        $notificationAccessor->getMessage()->willReturn('Something went wrong.');

        $this->shouldThrow(
            new NotificationExpectationMismatchException(
                'failure',
                'Something went wrong.',
                'success',
                'Something went wrong.'
            )
        )->during('checkFailureNotificationMessage', ['Something went wrong.']);
    }
}
