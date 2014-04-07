<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\SequenceBundle\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Sequence\Repository\SequenceRepositoryInterface;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class SequenceManagerSpec extends ObjectBehavior
{
    public function let(SequenceRepositoryInterface $repository, ObjectManager $manager)
    {
        $this->beConstructedWith($repository, $manager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SequenceBundle\Manager\SequenceManager');
    }

    function it_sets_next_index_when_sequence_exists($repository)
    {
        $repository->incrementIndex('order')->shouldBeCalled();
        $repository->getLastIndex('order')->shouldBeCalled()->willReturn(5);

        $this->setNextIndex('order')->shouldReturn(5);
    }
}
