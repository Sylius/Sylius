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
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Sylius\Component\User\Model\UserInterface;

/**
 * User register listener spec.
 *
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

    function it_canonicalize_user_email_and_username_on_pre_persist_doctrine_event($canonicalizer, LifecycleEventArgs $event, UserInterface $user)
    {
        $event->getEntity()->willReturn($user);

        $user->getUsername()->willReturn('testUser');
        $user->getEmail()->willReturn('test@email.com');

        $user->setUsernameCanonical('testuser')->shouldBeCalled();
        $user->setEmailCanonical('test@email.com')->shouldBeCalled();

        $canonicalizer->canonicalize('testUser')->willReturn('testuser')->shouldBeCalled();
        $canonicalizer->canonicalize('test@email.com')->willReturn('test@email.com')->shouldBeCalled();

        $this->prePersist($event);
    }

    function it_canonicalize_user_email_and_username_on_pre_update_doctrine_event($canonicalizer, LifecycleEventArgs $event, UserInterface $user)
    {
        $event->getEntity()->willReturn($user);

        $user->getUsername()->willReturn('testUser');
        $user->getEmail()->willReturn('test@email.com');

        $user->setUsernameCanonical('testuser')->shouldBeCalled();
        $user->setEmailCanonical('test@email.com')->shouldBeCalled();

        $canonicalizer->canonicalize('testUser')->willReturn('testuser')->shouldBeCalled();
        $canonicalizer->canonicalize('test@email.com')->willReturn('test@email.com')->shouldBeCalled();

        $this->preUpdate($event);
    }

    function it_canonicalize_only_user_interface_implementation($canonicalizer, LifecycleEventArgs $event, UserInterface $user)
    {
        $user='';
        $event->getEntity()->willReturn($user);

        $canonicalizer->canonicalize('testUser')->shouldNotBeCalled();
        $canonicalizer->canonicalize('test@email.com')->shouldNotBeCalled();

        $this->preUpdate($event);
    }
}
