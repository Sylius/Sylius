<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ArchetypeBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ArchetypeBundle\Exception\IncompatibleCollaboratorException;
use Sylius\Component\Archetype\Builder\ArchetypeBuilderInterface;
use Sylius\Component\Archetype\Model\ArchetypeInterface;
use Sylius\Component\Archetype\Model\ArchetypeSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Adam Elsodaney <adam.elso@gmail.com>
 */
class ArchetypeUpdateListenerSpec extends ObjectBehavior
{
    function let(ArchetypeBuilderInterface $builder, ObjectRepository $subjectRepository, ObjectManager $subjectManager)
    {
        $subjectRepository->getClassName()->willReturn(ArchetypeSubjectInterface::class);

        $this->beConstructedWith($builder, $subjectRepository, $subjectManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ArchetypeBundle\EventListener\ArchetypeUpdateListener');
    }

    function it_is_not_initializable_if_the_collaborating_repository_is_not_for_archetype_subjects(ArchetypeBuilderInterface $builder, ObjectRepository $nonSubjectRepository, ObjectManager $subjectManager)
    {
        $nonSubjectRepository->getClassName()->willReturn(\stdClass::class);

        $this->beConstructedWith($builder, $nonSubjectRepository, $subjectManager);

        $this->shouldThrow(IncompatibleCollaboratorException::class)->during('__construct', [$builder, $nonSubjectRepository, $subjectManager]);
    }

    function it_can_only_update_subjects_if_an_archetype_was_updated(GenericEvent $event, \stdClass $notAnArchetype, ArchetypeBuilderInterface $builder, ObjectRepository $subjectRepository, ObjectManager $subjectManager)
    {
        $event->getSubject()->willReturn($notAnArchetype);

        $subjectRepository->findBy(Argument::any())->shouldNotBeCalled();
        $builder->build(Argument::any())->shouldNotBeCalled();
        $subjectManager->persist(Argument::any())->shouldNotBeCalled();

        $this->shouldThrow(UnexpectedTypeException::class)->duringOnArchetypeUpdate($event);
    }

    function it_updates_subject_with_newer_attributes_added_to_their_archetypes(
        GenericEvent $event,
        ArchetypeInterface $archetype,
        ArchetypeBuilderInterface $builder,
        ObjectRepository $subjectRepository,
        ObjectManager $subjectManager,
        ArchetypeSubjectInterface $subjectA,
        ArchetypeSubjectInterface $subjectB
    ) {
        $event->getSubject()->willReturn($archetype);
        $subjectRepository->findBy(['archetype' => $archetype])->willReturn([$subjectA, $subjectB]);

        $builder->build($subjectA)->shouldBeCalled();
        $builder->build($subjectB)->shouldBeCalled();

        $subjectManager->persist($subjectA)->shouldBeCalled();
        $subjectManager->persist($subjectB)->shouldBeCalled();

        $this->onArchetypeUpdate($event);
    }
}
