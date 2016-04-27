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
use FOS\ElasticaBundle\Persister\ObjectPersister;
use Sylius\Component\Product\Model\AttributeValueInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTranslation;
use Sylius\Component\Variation\Model\VariantInterface;

/**
 * @author Nicolas Adler <nicolas.adler@openizi.com>
 */
class ElasticaProductListener implements EventSubscriber
{
    /**
     * @var ObjectPersister
     */
    protected $objectPersister;

    /**
     * @var array
     */
    protected $scheduledForUpdate = [];

    /**
     * @param ObjectPersister $objectPersister
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
            'onFlush',
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

    public function onFlush()
    {
        if (!empty($this->scheduledForUpdate)) {
            $this->objectPersister->replaceMany($this->scheduledForUpdate);
        }
    }

    /**
     * Update product when variant is updated
     *
     * @param mixed $entity
     */
    private function update($entity)
    {
        if ($entity instanceof VariantInterface) {
            $this->addScheduledForUpdate($entity->getObject());
        }

        if ($entity instanceof AttributeValueInterface) {
            $this->addScheduledForUpdate($entity->getSubject());
        }

        if ($entity instanceof ProductTranslation) {
            $this->addScheduledForUpdate($entity->getTranslatable());
        }
    }

    /**
     * @param ProductInterface|null $product
     */
    private function addScheduledForUpdate(ProductInterface $product = null)
    {
        if ($this->objectPersister->handlesObject($product)) {
            $this->scheduledForUpdate[] = $product;
        }
    }
}
