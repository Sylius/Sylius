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
use Doctrine\ORM\Events;
use Sylius\Component\Registry\NonExistingServiceException;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Sequence\Model\SequenceSubjectInterface;
use Sylius\Component\Sequence\Registry\NonExistingGeneratorException;
use Sylius\Component\Sequence\SyliusSequenceEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

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
     * @var SequenceSubjectInterface[]
     */
    protected $entitiesEnabled = array();

    /**
     * @var bool
     */
    protected $listenerEnabled = false;

    /**
     * @param ServiceRegistryInterface $registry
     * @param EventManager $eventManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        ServiceRegistryInterface $registry,
        EventManager $eventManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->registry      = $registry;
        $this->eventManager  = $eventManager;
        $this->eventDispatcher  = $eventDispatcher;
    }

    /**
     * Enable this listener for the given entity
     * This will apply the number generator when this entity will be flushed
     *
     * @param SequenceSubjectInterface $subject
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
     * Apply generator to all enabled entities
     *
     * @throws NonExistingGeneratorException if no generator is found for an enabled entity
     */
    public function preFlush()
    {
        foreach ($this->entitiesEnabled as $entity) {
            try {
                $generator = $this->registry->get($entity->getSequenceType());
            } catch (NonExistingServiceException $e) {
                throw new NonExistingGeneratorException($entity, $e);
            }

            $event = new GenericEvent($entity);

            $this->eventDispatcher->dispatch(
                sprintf(SyliusSequenceEvents::PRE_GENERATE, $entity->getSequenceType()),
                $event
            );

            $generator->generate($entity);

            $this->eventDispatcher->dispatch(
                sprintf(SyliusSequenceEvents::POST_GENERATE, $entity->getSequenceType()),
                $event
            );
        }
    }
}
