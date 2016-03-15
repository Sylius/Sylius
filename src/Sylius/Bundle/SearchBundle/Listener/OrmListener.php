<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SearchBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Sylius\Bundle\SearchBundle\Indexer\OrmIndexer;

/**
 * Orm Listener
 *
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class OrmListener implements EventSubscriber
{
    /**
     * @var
     */
    private $ormIndexer;

    /**
     * Objects scheduled for insertion and replacement
     */
    public $scheduledForInsertion = [];
    public $scheduledForDeletion = [];

    /**
     * @param OrmIndexer $ormIndexer
     */
    public function __construct(OrmIndexer $ormIndexer)
    {
        $this->ormIndexer = $ormIndexer;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            'postFlush',
            'postPersist',
            'postUpdate',
            'preRemove',
        ];
    }

    /**
     * Happens after updating a product
     *
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($this->ormIndexer->isObjectIndexable($entity)) {
            $this->scheduledForInsertion[] = $entity;
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($this->ormIndexer->isObjectIndexable($entity)) {
            $this->scheduledForInsertion[] = $entity;
        }
    }

    /**
     * @param PostFlushEventArgs $args
     */
    public function postFlush(PostFlushEventArgs $args)
    {
        $this->index($args);
    }

    /**
     * Happens before the deletion of a product
     *
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($this->ormIndexer->isObjectIndexable($entity)) {
            $this->scheduledForDeletion[] = ['id' => $entity->getId(), 'entity' => $entity];
        }
    }

    /**
     * @param PostFlushEventArgs $args
     */
    public function index(PostFlushEventArgs $args)
    {
        // workaround to avoid circular reference of entity manager on indexer service definition
        $this->ormIndexer->setEntityManager($args->getEntityManager());

        if (count($this->scheduledForInsertion)) {
            // trick to clear the array and avoid looping
            $scheduledForInsertion = $this->scheduledForInsertion;
            $this->scheduledForInsertion = [];

            $this->ormIndexer->insertMany($scheduledForInsertion);
        }

        if (count($this->scheduledForDeletion)) {
            //this uses dql
            $this->ormIndexer->removeMany($this->scheduledForDeletion);
        }
    }
}
