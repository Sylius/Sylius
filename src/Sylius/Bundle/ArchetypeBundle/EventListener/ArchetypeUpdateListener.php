<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ArchetypeBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Bundle\ArchetypeBundle\Exception\IncompatibleCollaboratorException;
use Sylius\Component\Archetype\Builder\ArchetypeBuilderInterface;
use Sylius\Component\Archetype\Model\ArchetypeInterface;
use Sylius\Component\Archetype\Model\ArchetypeSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Automatically synchronizes options and attributes from the updated
 * archetype to all instances of subjects that are defined by that archetype.
 *
 * For example, adding a new 'material' option to a 't-shirt' archetype, will
 * add that option to all 'product' subjects that are t-shirts.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Adam Elsodaney <adam.elso@gmail.com>
 */
class ArchetypeUpdateListener
{
    /**
     * @var ArchetypeBuilderInterface
     */
    protected $builder;

    /**
     * @var ObjectRepository
     */
    protected $subjectRepository;

    /**
     * @var ObjectManager
     */
    protected $subjectManager;

    /**
     * @param ArchetypeBuilderInterface $builder
     * @param ObjectRepository $subjectRepository
     * @param ObjectManager $subjectManager
     *
     * @throws IncompatibleCollaboratorException if the repository is not for an archetype subject.
     */
    public function __construct(ArchetypeBuilderInterface $builder, ObjectRepository $subjectRepository, ObjectManager $subjectManager)
    {
        if (!is_a($subjectRepository->getClassName(), ArchetypeSubjectInterface::class, true)) {
            throw new IncompatibleCollaboratorException(sprintf(
                'The collaborating repository "%s" holds resources of type "%s", and are not of type "%s".',
                get_class($subjectRepository), $subjectRepository->getClassName(), ArchetypeSubjectInterface::class
            ));
        }

        $this->builder = $builder;
        $this->subjectRepository = $subjectRepository;
        $this->subjectManager = $subjectManager;
    }

    /**
     * @param GenericEvent $event
     */
    public function onArchetypeUpdate(GenericEvent $event)
    {
        $archetype = $event->getSubject();

        if (!$archetype instanceof ArchetypeInterface) {
            throw new UnexpectedTypeException($archetype, ArchetypeInterface::class);
        }

        $subjects = $this->subjectRepository->findBy(['archetype' => $archetype]);

        foreach ($subjects as $subject) {
            $this->builder->build($subject);

            $this->subjectManager->persist($subject);
        }
    }
}
