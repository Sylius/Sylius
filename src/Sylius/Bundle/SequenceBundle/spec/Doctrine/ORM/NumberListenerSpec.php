<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\SequenceBundle\Doctrine\ORM;

use Doctrine\ORM\EntityManager;
use PhpSpec\ObjectBehavior;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\Common\EventManager;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Sequence\Model\Sequence;
use Sylius\Component\Sequence\Model\SequenceSubjectInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Sequence\Number\GeneratorInterface;
use Sylius\Component\Sequence\SyliusSequenceEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class NumberListenerSpec extends ObjectBehavior
{
    function let(
        ServiceRegistryInterface $registry,
        EventManager $eventManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->beConstructedWith(
            $registry,
            $eventManager,
            $eventDispatcher,
            'Sylius\Component\Sequence\Model\Sequence'
        );
    }

    function it_enable_listener_on_specific_entity(SequenceSubjectInterface $subject, $eventManager)
    {
        $eventManager->addEventListener(
            Events::preFlush,
            Argument::type('Sylius\Bundle\SequenceBundle\Doctrine\ORM\NumberListener')
        )->shouldBeCalled();

        $this->enableEntity($subject);
    }

    function it_applies_generator(
        PreFlushEventArgs $args,
        EntityManager $entityManager,
        SequenceSubjectInterface $entity,
        GeneratorInterface $generator,
        EntityRepository $sequenceRepository,
        Sequence $sequence,
        $registry,
        $eventDispatcher
    ) {
        $this->enableEntity($entity);

        $args->getEntityManager()->willReturn($entityManager);

        $registry->get($entity)
            ->willReturn($generator);

        $entity->getSequenceType()->willReturn('sequence_type');

        $entityManager->getRepository('Sylius\Component\Sequence\Model\Sequence')->willReturn($sequenceRepository);
        $sequenceRepository->findOneBy(array('type' => 'sequence_type'))->willReturn($sequence);

        $eventDispatcher->dispatch(
            sprintf(SyliusSequenceEvents::PRE_GENERATE, 'sequence_type'),
            Argument::type('Symfony\Component\EventDispatcher\GenericEvent')
        )->shouldBeCalled();

        $generator->generate($entity, $sequence)->shouldBeCalled();

        $eventDispatcher->dispatch(
            sprintf(SyliusSequenceEvents::POST_GENERATE, 'sequence_type'),
            Argument::type('Symfony\Component\EventDispatcher\GenericEvent')
        )->shouldBeCalled();

        $this->preFlush($args);
    }
} 