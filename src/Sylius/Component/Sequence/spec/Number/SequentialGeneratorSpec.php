<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Sequence\Number;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Sequence\Model\SequenceInterface;
use Sylius\Component\Sequence\Model\SequenceSubjectInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SequentialGeneratorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Sequence\Number\SequentialGenerator');
    }

    function it_generates_000001_number_for_first_subject(
        SequenceSubjectInterface $subject,
        SequenceInterface $sequence
    ) {
        $subject->getNumber()->willReturn(null);
        $subject->getSequenceType()->willReturn('order');

        $sequence->getIndex()->willReturn(0);
        $sequence->incrementIndex()->shouldBeCalled();

        $subject->setNumber('000000001')->shouldBeCalled();

        $this->generate($subject, $sequence);
    }

    function it_generates_a_correct_number_for_following_subjects(
        SequenceSubjectInterface $subject,
        SequenceInterface $sequence
    ) {
        $subject->getNumber()->willReturn(null);
        $subject->getSequenceType()->willReturn('order');

        $sequence->getIndex()->willReturn(222);
        $sequence->incrementIndex()->shouldBeCalled();

        $subject->setNumber('000000223')->shouldBeCalled();

        $this->generate($subject, $sequence);
    }

    function it_starts_at_start_number_if_specified(SequenceSubjectInterface $subject, SequenceInterface $sequence)
    {
        $this->beConstructedWith(6, 123);

        $subject->getNumber()->willReturn(null);
        $subject->getSequenceType()->willReturn('order');

        $sequence->getIndex()->willReturn(0);
        $sequence->incrementIndex()->shouldBeCalled();

        $subject->setNumber('000123')->shouldBeCalled();

        $this->generate($subject, $sequence);
    }

    function it_leaves_existing_numbers_alone(SequenceSubjectInterface $subject, SequenceInterface $sequence)
    {
        $subject->getNumber()->willReturn('123');
        $subject->setNumber(Argument::any())->shouldNotBeCalled();

        $sequence->incrementIndex()->shouldNotBeCalled();

        $this->generate($subject, $sequence);
    }
}
