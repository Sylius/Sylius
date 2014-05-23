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
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Events;
use Sylius\Component\Registry\NonExistingServiceException;
use Sylius\Component\Sequence\Registry\NonExistingGeneratorException;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Sequence\Model\SequenceSubjectInterface;
use Sylius\Component\Sequence\SyliusSequenceEvents;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Doctrine event listener
 *
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
     * @var array
     */
    protected $entitiesEnabled = array();

    /**
     * @var bool
     */
    protected $listenerEnabled = false;

    public function __construct(
        ServiceRegistryInterface $registry,
        EventManager $eventManager,
        EventDispatcherInterface $eventDispatcher,
        $sequenceClass
    ) {
        $this->registry      = $registry;
        $this->eventManager  = $eventManager;
        $this->eventDispatcher  = $eventDispatcher;
        $this->sequenceClass = $sequenceClass;
    }

    /**
     * Enable this listener for the given entity
     * This will apply the number generator when this entity will be fushed
     *
     * @param SequenceSubjectInterface $subject
     */
    public function enableEntity(SequenceSubjectInterface $subject)
    {
        $this->entitiesEnabled[spl_object_hash($subject)] = true;

        if (!$this->listenerEnabled) {
            $this->eventManager->addEventListener(Events::preFlush, $this);
            $this->listenerEnabled = true;
        }
    }

    /**
     * Apply generator to all enabled entities
     *
     * @param PreFlushEventArgs $args
     */
    public function preFlush(PreFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach (array_merge($uow->getScheduledEntityUpdates(), $uow->getScheduledEntityInsertions()) as $entity) {
            if ($this->isEntityEnabled($entity)) {
                $event = new GenericEvent($entity);

                try {
                    $generator = $this->registry->get($entity);
                } catch (NonExistingServiceException $e) {
                    throw new NonExistingGeneratorException($entity, $e);
                }

                $sequence = $em
                    ->getRepository($this->sequenceClass)
                    ->findOneByType($entity->getSequenceType())
                ;

                if (null === $sequence) {
                    $sequence = new $this->sequenceClass($entity->getSequenceType());
                }

                $this->eventDispatcher->dispatch(
                    sprintf(SyliusSequenceEvents::PRE_GENERATE, $entity->getSequenceType()),
                    $event
                );

                $generator->generate($entity, $sequence);

                $this->eventDispatcher->dispatch(
                    sprintf(SyliusSequenceEvents::POST_GENERATE, $entity->getSequenceType()),
                    $event
                );

                $em->persist($sequence);
            }
        }
    }

    /**
     * @param $entity
     * @return bool
     */
    protected function isEntityEnabled($entity)
    {
        return isset($this->entitiesEnabled[spl_object_hash($entity)]);
    }
}
