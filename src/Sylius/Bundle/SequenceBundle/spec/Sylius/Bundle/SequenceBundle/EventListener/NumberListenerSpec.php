<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\SequenceBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\SequenceBundle\Doctrine\ORM\NumberListener;
use Sylius\Component\Sequence\Model\SequenceSubjectInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class NumberListenerSpec extends ObjectBehavior
{
    function let(NumberListener $listener)
    {
        $this->beConstructedWith($listener);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SequenceBundle\EventListener\NumberListener');
    }

    function it_generates_number($listener, GenericEvent $event, SequenceSubjectInterface $subject)
    {
        $event->getSubject()->willReturn($subject);

        $subject->getNumber()->willReturn(null);

        $listener->enableEntity($subject)->shouldBeCalled();

        $this->generateNumber($event);
    }
}
