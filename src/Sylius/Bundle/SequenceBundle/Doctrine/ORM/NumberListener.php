<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SequenceBundle\Doctrine\ORM;

use Doctrine\Common\EventManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Events;
use Sylius\Component\Registry\NonExistingServiceException;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Sequence\Model\SequenceInterface;
use Sylius\Component\Sequence\Model\SequenceSubjectInterface;
use Sylius\Component\Sequence\Registry\NonExistingGeneratorException;
use Sylius\Component\Sequence\SyliusSequenceEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class NumberListener
{
    /**
     * @var ServiceRegistryInterface
     */
    protected $registry;

    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var string
     */
    protected $sequenceClass;

    /**
     * @var SequenceSubjectInterface[]
     */
    protected $entitiesEnabled = [];

    /**
     * @var array
     */
    protected $sequences = [];

    /**
     * @var bool
     */
    protected $listenerEnabled = false;

    /**
     * @param ServiceRegistryInterface $registry
     * @param EventManager $eventManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param string $sequenceClass
     */
    public function __construct(
        ServiceRegistryInterface $registry,
        EventManager $eventManager,
        EventDispatcherInterface $eventDispatcher,
        $sequenceClass
    ) {
        $this->registry = $registry;
        $this->eventManager = $eventManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->sequenceClass = $sequenceClass;
    }

    /**
     * {@inheritdoc}
     */
    public function enableEntity(SequenceSubjectInterface $subject)
    {
        $this->entitiesEnabled[spl_object_hash($subject)] = $subject;

        if (!$this->listenerEnabled) {
            $this->eventManager->addEventListener(Events::preFlush, $this);
            $this->listenerEnabled = true;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function preFlush(PreFlushEventArgs $args)
    {
        $em = $args->getEntityManager();

        foreach ($this->entitiesEnabled as $entity) {
            try {
                $generator = $this->registry->get($entity);
            } catch (NonExistingServiceException $e) {
                throw new NonExistingGeneratorException($entity, $e);
            }

            $sequence = $this->getSequence($entity->getSequenceType(), $em);

            $event = new GenericEvent($entity);

            $this->eventDispatcher->dispatch(
                sprintf(SyliusSequenceEvents::PRE_GENERATE, $entity->getSequenceType()),
                $event
            );

            $generator->generate($entity, $sequence);

            $this->eventDispatcher->dispatch(
                sprintf(SyliusSequenceEvents::POST_GENERATE, $entity->getSequenceType()),
                $event
            );
        }
    }

    /**
     * @param string $type
     * @param EntityManagerInterface $entityManager
     *
     * @return SequenceInterface
     */
    protected function getSequence($type, EntityManagerInterface $entityManager)
    {
        if (isset($this->sequences[$type])) {
            return $this->sequences[$type];
        }

        $sequence = $entityManager
            ->getRepository($this->sequenceClass)
            ->findOneBy(['type' => $type])
        ;

        if (null === $sequence) {
            $sequence = new $this->sequenceClass($type);
            $entityManager->persist($sequence);
        }

        return $this->sequences[$type] = $sequence;
    }
}
