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
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Sylius\Component\Sequence\Model\SequenceSubjectInterface;
use Sylius\Component\Sequence\Registry\GeneratorRegistry;

/**
 * Doctrine event listener
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class NumberListener
{
    /**
     * @var GeneratorRegistry
     */
    protected $registry;

    /**
     * @var EventManager
     */
    protected $eventManager;

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

    public function __construct(GeneratorRegistry $registry, EventManager $eventManager, $sequenceClass)
    {
        $this->registry      = $registry;
        $this->eventManager  = $eventManager;
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
            $this->eventManager->addEventListener(Events::onFlush, $this);
            $this->listenerEnabled = true;
        }
    }

    /**
     * Apply generator to all enabled entities
     *
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach (array_merge($uow->getScheduledEntityUpdates(), $uow->getScheduledEntityInsertions()) as $entity) {
            if (
                $this->isEntityEnabled($entity)
                && null !== $generator = $this->registry->getGenerator($entity)
            ) {
                $sequence = $em
                    ->getRepository($this->sequenceClass)
                    ->findOneByType($entity->getSequenceType())
                ;

                if (null === $sequence) {
                    $sequence = new $this->sequenceClass($entity->getSequenceType());
                }

                $generator->generate($entity, $sequence);

                $em->persist($sequence);
                $uow->computeChangeSet($em->getClassMetadata(get_class($sequence)), $sequence);
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
