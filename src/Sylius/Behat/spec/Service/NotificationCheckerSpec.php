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

    function it_checks_if_successful_creation_notifaction_has_appeared(NotificationAccessorInterface $notificationAccessor)
    {
        $notificationAccessor->hasSuccessMessage()->willReturn(true);
        $notificationAccessor->hasMessage('Success Some resource has been successfully created.')->willReturn(true);

        $this->checkCreationNotification('some_resource');
    }

    function it_checks_if_successful_edition_notifaction_has_appeared(NotificationAccessorInterface $notificationAccessor)
    {
        $notificationAccessor->hasSuccessMessage()->willReturn(true);
        $notificationAccessor->hasMessage('Success Some resource has been successfully updated.')->willReturn(true);

        $this->checkEditionNotification('some_resource');
    }

    function it_checks_if_successful_deletion_notifaction_has_appeared(NotificationAccessorInterface $notificationAccessor)
    {
        $notificationAccessor->hasSuccessMessage()->willReturn(true);
        $notificationAccessor->hasMessage('Success Some resource has been successfully deleted.')->willReturn(true);

        $this->checkDeletionNotification('some_resource');
    }

    function it_checks_if_successful_notifaction_has_appeared(NotificationAccessorInterface $notificationAccessor)
    {
        $notificationAccessor->hasSuccessMessage()->willReturn(true);
        $notificationAccessor->hasMessage('Success Some resource has been successfully deleted.')->willReturn(true);

        $this->checkSuccessNotificationMessage('Success Some resource has been successfully deleted.');
    }

    function it_throws_notification_mismatch_exception_if_diffrent_or_no_notifaction_has_been_found(
        NotificationAccessorInterface $notificationAccessor
    ) {
        $notificationAccessor->hasSuccessMessage()->willReturn(true);
        $notificationAccessor->hasMessage('Success Some resource has been successfully created.')->willReturn(false);
        $notificationAccessor->getMessageType()->willReturn('success');
        $notificationAccessor->getMessage()->willReturn('Success Some resource has been successfully updated.');

        $this->shouldThrow(
            new NotificationExpectationMismatchException(
                'success', 
                'Success Some resource has been successfully created.',
                'success', 
                'Success Some resource has been successfully updated.'
            )
        )->during('checkSuccessNotificationMessage', ['Success Some resource has been successfully created.']);
    }

    function it_throws_notification_mismatch_exception_if_diffrent_message_type_has_been_found(
        NotificationAccessorInterface $notificationAccessor
    ) {
        $notificationAccessor->hasSuccessMessage()->willReturn(false);
        $notificationAccessor->hasMessage('Success Some resource has been successfully created.')->willReturn(false);
        $notificationAccessor->getMessageType()->willReturn('failure');
        $notificationAccessor->getMessage()->willReturn('Success Some resource has been successfully created.');

        $this->shouldThrow(
            new NotificationExpectationMismatchException(
                'success',
                'Success Some resource has been successfully created.',
                'failure',
                'Success Some resource has been successfully created.'
            )
        )->during('checkSuccessNotificationMessage', ['Success Some resource has been successfully created.']);
    }
}
