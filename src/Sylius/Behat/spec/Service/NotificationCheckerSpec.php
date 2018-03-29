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

namespace spec\Sylius\Behat\Service;

use Behat\Mink\Element\NodeElement;
use PhpSpec\ObjectBehavior;
use Sylius\Behat\Exception\NotificationExpectationMismatchException;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Service\Accessor\NotificationAccessorInterface;
use Sylius\Behat\Service\NotificationChecker;
use Sylius\Behat\Service\NotificationCheckerInterface;

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

    function it_checks_if_successful_notification_with_specific_message_has_appeared(
        NotificationAccessorInterface $notificationAccessor,
        NodeElement $firstMessage,
        NodeElement $secondMessage
    ) {
        $notificationAccessor->getMessageElements()->willReturn([$firstMessage, $secondMessage]);

        $firstMessage->getText()->willReturn('Some resource has been successfully edited.');
        $firstMessage->hasClass('positive')->willReturn(true);

        $secondMessage->getText()->willReturn('Some resource has been successfully deleted.');
        $secondMessage->hasClass('positive')->willReturn(true);

        $this->checkNotification('Some resource has been successfully deleted.', NotificationType::success());
    }

    function it_checks_if_failure_notification_with_specific_message_has_appeared(
        NotificationAccessorInterface $notificationAccessor,
        NodeElement $firstMessage,
        NodeElement $secondMessage
    ) {
        $notificationAccessor->getMessageElements()->willReturn([$firstMessage, $secondMessage]);

        $firstMessage->getText()->willReturn('Some resource has been successfully edited.');
        $firstMessage->hasClass('negative')->willReturn(false);

        $secondMessage->getText()->willReturn('Some resource could not be deleted.');
        $secondMessage->hasClass('negative')->willReturn(true);

        $this->checkNotification('Some resource could not be deleted.', NotificationType::failure());
    }

    function it_throws_exception_if_no_message_with_given_content_and_type_has_been_found(
        NotificationAccessorInterface $notificationAccessor,
        NodeElement $firstMessage,
        NodeElement $secondMessage
    ) {
        $notificationAccessor->getMessageElements()->willReturn([$firstMessage, $secondMessage]);

        $firstMessage->getText()->willReturn('Some resource has been successfully edited.');
        $firstMessage->hasClass('negative')->willReturn(false);

        $secondMessage->getText()->willReturn('Some resource could not be deleted.');
        $secondMessage->hasClass('negative')->willReturn(true);

        $this
            ->shouldThrow(NotificationExpectationMismatchException::class)
            ->during('checkNotification', ['Some resource has been successfully created.', NotificationType::failure()])
        ;
    }
}
