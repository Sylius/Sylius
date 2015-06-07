<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Sequence\Generator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Sequence\Model\SequenceInterface;
use Sylius\Component\Sequence\Model\SequenceSubjectInterface;
use Sylius\Component\Sequence\Provider\SequenceProviderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SequentialGeneratorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Sequence\Generator\SequentialGenerator');
    }

    function let(SequenceProviderInterface $sequenceProvider, SequenceInterface $sequence)
    {
        $sequenceProvider->getSequence('order')->willReturn($sequence);
        $sequence->getIndex()->willReturn(0);

        $this->beConstructedWith($sequenceProvider);
    }

    function it_generates_and_increments_sequence(SequenceSubjectInterface $subject, SequenceInterface $sequence)
    {
        $subject->getNumber()->willReturn(null);
        $subject->getSequenceType()->willReturn('order');

        $sequence->incrementIndex()->shouldBeCalled();
        $subject->setNumber('000000001')->shouldBeCalled();

        $this->generate($subject);
    }

    function it_generates_a_correct_number_for_following_subjects(SequenceSubjectInterface $subject, SequenceInterface $sequence)
    {
        $subject->getNumber()->willReturn(null);
        $subject->getSequenceType()->willReturn('order');
        $sequence->getIndex()->willReturn(222);

        $sequence->incrementIndex()->shouldBeCalled();
        $subject->setNumber('000000223')->shouldBeCalled();

        $this->generate($subject);
    }

    function it_correctly_pads_number(SequenceProviderInterface $sequenceProvider, SequenceSubjectInterface $subject, SequenceInterface $sequence)
    {
        $this->beConstructedWith($sequenceProvider, 4);

        $subject->getNumber()->willReturn(null);
        $subject->getSequenceType()->willReturn('order');

        $sequence->getIndex()->willReturn(122);
        $sequence->incrementIndex()->shouldBeCalled();

        $subject->setNumber('0123')->shouldBeCalled();

        $this->generate($subject, $sequence);
    }

    function it_can_omit_number_padding(SequenceProviderInterface $sequenceProvider, SequenceSubjectInterface $subject, SequenceInterface $sequence)
    {
        $this->beConstructedWith($sequenceProvider, 0);

        $subject->getNumber()->willReturn(null);
        $subject->getSequenceType()->willReturn('order');

        $sequence->getIndex()->willReturn(122);
        $sequence->incrementIndex()->shouldBeCalled();

        $subject->setNumber('123')->shouldBeCalled();

        $this->generate($subject, $sequence);
    }

    function it_starts_at_start_number_if_specified(
        SequenceProviderInterface $sequenceProvider,
        SequenceSubjectInterface $subject,
        SequenceInterface $sequence
    ) {
        $this->beConstructedWith($sequenceProvider, 9, 123);

        $subject->getNumber()->willReturn(null);
        $subject->getSequenceType()->willReturn('order');

        $sequence->getIndex()->willReturn(0);
        $sequence->incrementIndex()->shouldBeCalled();

        $subject->setNumber('000000123')->shouldBeCalled();

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
