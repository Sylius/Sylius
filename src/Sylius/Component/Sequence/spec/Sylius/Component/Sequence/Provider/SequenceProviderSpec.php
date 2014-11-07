<?php

namespace spec\Sylius\Component\Sequence\Provider;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Sequence\Model\SequenceInterface;

class SequenceProviderSpec extends ObjectBehavior
{
    function let(RepositoryInterface $repository, ObjectManager $manager)
    {
        $this->beConstructedWith($repository, $manager, array('order' => 123));
    }

    function it_creates_new_sequence_if_sequence_does_not_exist(
        RepositoryInterface $repository,
        ObjectManager $manager,
        SequenceInterface $sequence
    ) {
        $repository->findOneBy(array('type' => 'order'))->willReturn(null);
        $repository->createNew()->shouldBeCalled()->willReturn($sequence);
        $sequence->setIndex(123)->shouldBeCalled();
        $sequence->setType('order')->shouldBeCalled();
        $manager->persist($sequence)->shouldBeCalled();

        $this->getSequence('order')->shouldReturn($sequence);
    }

    function it_returns_existing_sequences(
        RepositoryInterface $repository,
        SequenceInterface $sequence
    ) {
        $repository->findOneBy(array('type' => 'order'))->willReturn($sequence);
        $repository->createNew()->shouldNotBeCalled();

        $this->getSequence('order')->shouldReturn($sequence);
    }
}
