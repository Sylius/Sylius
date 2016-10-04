<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Manuel Gonzalez <mgonyan@gmail.com>
 */
final class MailerListenerSpec extends ObjectBehavior
{
    function let(SenderInterface $emailSender)
    {
        $this->beConstructedWith($emailSender);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\MailerListener');
    }

    function it_throws_an_exception_if_event_subject_is_not_a_customer_instance_sending_confirmation(
        GenericEvent $event
    ) {
        $customerClass = new \stdClass();

        $event->getSubject()->shouldBeCalled()->willReturn($customerClass);

        $exception = new UnexpectedTypeException(
            $customerClass,
            CustomerInterface::class
        );

        $this->shouldThrow($exception)->duringSendUserConfirmationEmail($event);
    }

    function it_should_not_send_the_email_confirmation_if_the_customer_user_is_null(
        GenericEvent $event,
        SenderInterface $emailSender,
        CustomerInterface $customer
    ) {
        $event->getSubject()->shouldBeCalled()->willReturn($customer);

        $customer->getUser()->shouldBeCalled()->willReturn(null);

        $emailSender->send(Argument::any(), Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->sendUserConfirmationEmail($event)->shouldReturn(null);
    }

    function it_should_not_send_the_email_confirmation_if_the_customer_user_is_not_enabled(
        GenericEvent $event,
        SenderInterface $emailSender,
        CustomerInterface $customer,
        UserInterface $user
    ) {
        $event->getSubject()->shouldBeCalled()->willReturn($customer);

        $customer->getUser()->shouldBeCalled()->willReturn($user);

        $user->isEnabled()->shouldBeCalled()->willReturn(false);

        $emailSender->send(Argument::any(), Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->sendUserConfirmationEmail($event)->shouldReturn(null);
    }

    function it_should_not_send_the_email_confirmation_if_the_customer_user_does_not_have_email(
        GenericEvent $event,
        SenderInterface $emailSender,
        CustomerInterface $customer,
        UserInterface $user
    ) {
        $event->getSubject()->shouldBeCalled()->willReturn($customer);

        $customer->getUser()->shouldBeCalled()->willReturn($user);
        $customer->getEmail()->shouldBeCalled()->willReturn(null);

        $user->isEnabled()->shouldBeCalled()->willReturn(true);

        $emailSender->send(Argument::any(), Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->sendUserConfirmationEmail($event)->shouldReturn(null);
    }

    function it_sends_email_confirmation_successfully(
        GenericEvent $event,
        SenderInterface $emailSender,
        CustomerInterface $customer,
        UserInterface $user
    ) {
        $event->getSubject()->shouldBeCalled()->willReturn($customer);

        $customer->getUser()->shouldBeCalled()->willReturn($user);
        $customer->getEmail()->shouldBeCalled()->willReturn('fulanito@sylius.com');

        $user->isEnabled()->shouldBeCalled()->willReturn(true);

        $emailSender
            ->send(Emails::USER_CONFIRMATION, ['fulanito@sylius.com'], ['user' => $user])
            ->shouldBeCalled()
        ;

        $this->sendUserConfirmationEmail($event)->shouldReturn(null);
    }
}
