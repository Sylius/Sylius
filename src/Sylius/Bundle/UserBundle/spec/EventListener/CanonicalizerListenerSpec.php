<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\UserBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Sylius\Component\User\Model\CustomerInterface;
use Sylius\Component\User\Model\UserInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class CanonicalizerListenerSpec extends ObjectBehavior
{
    function let(CanonicalizerInterface $canonicalizer)
    {
        $this->beConstructedWith($canonicalizer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\EventListener\CanonicalizerListener');
    }

    function it_canonicalize_user_username_on_pre_persist_doctrine_event($canonicalizer, LifecycleEventArgs $event, UserInterface $user)
    {
        $event->getEntity()->willReturn($user);
        $user->getUsername()->willReturn('testUser');

        $user->setUsernameCanonical('testuser')->shouldBeCalled();
        $canonicalizer->canonicalize('testUser')->willReturn('testuser')->shouldBeCalled();

        $this->prePersist($event);
    }

    function it_canonicalize_customer_email_on_pre_persist_doctrine_event($canonicalizer, LifecycleEventArgs $event, CustomerInterface $customer)
    {
        $event->getEntity()->willReturn($customer);
        $customer->getEmail()->willReturn('testUser@Email.com');

        $customer->setEmailCanonical('testuser@email.com')->shouldBeCalled();
        $canonicalizer->canonicalize('testUser@Email.com')->willReturn('testuser@email.com')->shouldBeCalled();

        $this->prePersist($event);
    }

    function it_canonicalize_user_username_on_pre_update_doctrine_event($canonicalizer, LifecycleEventArgs $event, UserInterface $user)
    {
        $event->getEntity()->willReturn($user);
        $user->getUsername()->willReturn('testUser');

        $user->setUsernameCanonical('testuser')->shouldBeCalled();
        $canonicalizer->canonicalize('testUser')->willReturn('testuser')->shouldBeCalled();

        $this->preUpdate($event);
    }

    function it_canonicalize_customer_email_on_pre_update_doctrine_event($canonicalizer, LifecycleEventArgs $event, CustomerInterface $customer)
    {
        $event->getEntity()->willReturn($customer);
        $customer->getEmail()->willReturn('testUser@Email.com');

        $customer->setEmailCanonical('testuser@email.com')->shouldBeCalled();
        $canonicalizer->canonicalize('testUser@Email.com')->willReturn('testuser@email.com')->shouldBeCalled();

        $this->preUpdate($event);
    }

    function it_canonicalize_only_user_or_customer_interface_implementation_on_pre_presist($canonicalizer, LifecycleEventArgs $event)
    {
        $item = new \stdClass();
        $event->getEntity()->willReturn($item);

        $canonicalizer->canonicalize(Argument::any())->shouldNotBeCalled();

        $this->prePersist($event);
    }

    function it_canonicalize_only_user_or_customer_interface_implementation_on_pre_update($canonicalizer, LifecycleEventArgs $event)
    {
        $item = new \stdClass();
        $event->getEntity()->willReturn($item);

        $canonicalizer->canonicalize(Argument::any())->shouldNotBeCalled();

        $this->preUpdate($event);
    }
}
