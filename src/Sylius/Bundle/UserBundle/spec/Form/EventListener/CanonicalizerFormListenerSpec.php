<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\UserBundle\Form\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Form\FormEvent;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class CanonicalizerFormListenerSpec extends ObjectBehavior
{
    function let(CanonicalizerInterface $canonicalizer)
    {
        $this->beConstructedWith($canonicalizer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\Form\EventListener\CanonicalizerFormListener');
    }

    function it_implements_event_subscriber_interface()
    {
        $this->shouldImplement('Symfony\Component\EventDispatcher\EventSubscriberInterface');
    }

    function it_canonicalize_user_email_and_username($canonicalizer, FormEvent $event, UserInterface $user)
    {
        $event->getData()->willReturn($user);

        $user->getUsername()->willReturn('testUser');
        $user->getEmail()->willReturn('test@user.com');
        $user->setUsernameCanonical('testuser')->shouldBeCalled();
        $user->setEmailCanonical('test@user.com')->shouldBeCalled();

        $canonicalizer->canonicalize('testUser')->willReturn('testuser');
        $canonicalizer->canonicalize('test@user.com')->willReturn('test@user.com');

        $this->submit($event);
    }

    function it_affects_only_on_user_interface_implementations(FormEvent $event)
    {
        $user = '';
        $event->getData()->willReturn($user);
        $this->shouldThrow(new UnexpectedTypeException($user, 'Sylius\Component\User\Model\UserInterface'))
            ->duringSubmit($event);
    }
}
