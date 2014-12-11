<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Sequence\Provider;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Sequence\Model\SequenceInterface;

/**
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class SequenceProviderSpec extends ObjectBehavior
{
    function let(RepositoryInterface $repository, ObjectManager $manager)
    {
        $this->beConstructedWith($repository, $manager, array('order' => 123));
    }

    function it_creates_new_sequence_if_sequence_does_not_exist(
        RepositoryInterface $repository,
        ObjectManager $manager
    ) {
        $sequenceClass = 'Sylius\Component\Sequence\Model\Sequence';

        $repository->findOneBy(array('type' => 'order'))->willReturn(null);
        $repository->getClassName()->shouldBeCalled()->willReturn($sequenceClass);
        $manager->persist(Argument::type($sequenceClass))->shouldBeCalled();

        $this->getSequence('order')->shouldReturnAnInstanceOf($sequenceClass);
    }

    function it_returns_existing_sequences(
        RepositoryInterface $repository,
        SequenceInterface $sequence,
        ObjectManager $manager
    ) {
        $repository->findOneBy(array('type' => 'order'))->willReturn($sequence);
        $manager->persist(Argument::any())->shouldNotBeCalled();

        $this->getSequence('order')->shouldReturn($sequence);
    }
}
