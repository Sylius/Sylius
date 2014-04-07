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
use Sylius\Component\Sequence\Manager\SequenceManagerInterface;
use Sylius\Component\Sequence\Model\SequenceSubjectInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SequentialGeneratorSpec extends ObjectBehavior
{
    public function let(SequenceManagerInterface $manager)
    {
        $this->beConstructedWith($manager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Sequence\Number\SequentialGenerator');
    }

    function it_generates_000001_number_for_first_subject($manager, SequenceSubjectInterface $subject)
    {
        $subject->getNumber()->willReturn(null);
        $subject->getSequenceType()->willReturn('order');

        $manager->setNextIndex('order')->shouldBeCalled()->willReturn(0);
        $subject->setNumber('000000001')->shouldBeCalled();

        $this->generate($subject);
    }

    function it_generates_a_correct_number_for_following_subjects($manager, SequenceSubjectInterface $subject)
    {
        $subject->getNumber()->willReturn(null);
        $subject->getSequenceType()->willReturn('order');

        $manager->setNextIndex('order')->shouldBeCalled()->willReturn(222);
        $subject->setNumber('000000223')->shouldBeCalled();

        $this->generate($subject);
    }

    function it_starts_at_start_number_if_specified($manager, SequenceSubjectInterface $subject)
    {
        $this->beConstructedWith($manager, 6, 123);

        $subject->getNumber()->willReturn(null);
        $subject->getSequenceType()->willReturn('order');

        $manager->setNextIndex('order')->shouldBeCalled()->willReturn(0);
        $subject->setNumber('000123')->shouldBeCalled();

        $this->generate($subject);
    }

    function it_leaves_existing_numbers_alone(SequenceSubjectInterface $subject)
    {
        $subject->getNumber()->willReturn('123');
        $subject->setNumber(Argument::any())->shouldNotBeCalled();

        $this->generate($subject);
    }
}
