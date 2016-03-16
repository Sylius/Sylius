<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use FOS\ElasticaBundle\Persister\ObjectPersister;
use Sylius\Component\Product\Model\AttributeValueInterface;
use Sylius\Component\Core\Model\ProductTranslation;
use Sylius\Component\Variation\Model\VariantInterface;

/**
 * Elastica product listener
 *
 * @author Nicolas Adler <nicolas.adler@openizi.com>
 */
class ElasticaProductListener implements EventSubscriber
{
    /**
     * @var ObjectPersister
     */
    protected $objectPersister;

    /**
     * Objects scheduled for update
     */
    public $scheduledForUpdate = [];

    /**
     * @param OrmIndexer $ormIndexer
     */
    public function __construct(ObjectPersister $objectPersister)
    {
        $this->objectPersister = $objectPersister;
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
            'postRemove',
        ];
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $this->update($eventArgs->getObject());
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function postUpdate(LifecycleEventArgs $eventArgs)
    {
        $this->update($eventArgs->getObject());
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     */
    public function postRemove(LifecycleEventArgs $eventArgs)
    {
        $this->update($eventArgs->getObject());
    }

    /**
     * Update product when variant is updated
     *
     * @param  $entity
     */
    private function update($entity)
    {
        if ($entity instanceof VariantInterface) {
            $product = $entity->getObject();
            if ($this->objectPersister->handlesObject($product)) {
                $this->scheduledForUpdate[] = $product;
            }
        }

        if ($entity instanceof AttributeValueInterface) {
            $product = $entity->getSubject();
            if ($this->objectPersister->handlesObject($product)) {
                $this->scheduledForUpdate[] = $product;
            }
        }

        if ($entity instanceof ProductTranslation) {
            $product = $entity->getTranslatable();
            if ($this->objectPersister->handlesObject($product)) {
                $this->scheduledForUpdate[] = $product;
            }
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
     * @param PostFlushEventArgs $args
     */
    public function index(PostFlushEventArgs $args)
    {
        if (count($this->scheduledForUpdate)) {
            $this->objectPersister->replaceMany($this->scheduledForUpdate);
        }
    }
}
