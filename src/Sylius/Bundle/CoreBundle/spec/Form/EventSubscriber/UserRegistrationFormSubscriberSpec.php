<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Form\EventSubscriber;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Form\EventSubscriber\UserRegistrationFormSubscriber;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class UserRegistrationFormSubscriberSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(UserRegistrationFormSubscriber::class);
    }

    function it_is_an_event_subscriber()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_listens_on_submit()
    {
        $this->getSubscribedEvents()->shouldReturn([FormEvents::SUBMIT => 'submit']);
    }

    function it_throws_unexpected_type_excepotion_if_data_is_not_customer_type(
        FormEvent $event,
        ShopUserInterface $user
    ){
        $event->getData()->willReturn($user);

        $this->shouldThrow(UnexpectedTypeException::class)->during('submit', [$event]);
    }

    function it_set_user_as_enabled_if_customer_has_user(
        FormEvent $event,
        CustomerInterface $customer,
        ShopUserInterface $user
    ) {
        $event->getData()->willReturn($customer);
        $customer->getUser()->willReturn($user);

        $user->setEnabled(true)->shouldBeCalled();

        $this->submit($event);
    }
}
