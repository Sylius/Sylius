<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\Doctrine\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LoadClassMetadataEventArgs;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata as ODMClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadata as ORMClassMetadata;

/**
 * Doctrine subscriber which able to add own association object
 *
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
class AssociationInheritanceMetadataSubscriber implements EventSubscriber
{
    const ASSOCIATION_CLASS_NAME = 'Sylius\Component\Product\Model\Association';

    /**
     * @var array
     */
    private $discriminatorMap = array();

    public function __construct($discriminatorMap)
    {
        $this->discriminatorMap = $discriminatorMap;
    }

    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents()
    {
        return array('loadClassMetadata');
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        /** @var $metadata ORMClassMetadata|ODMClassMetadata */
        $metadata = $eventArgs->getClassMetadata();
        if (self::ASSOCIATION_CLASS_NAME !== $metadata->getName()) {
            return;
        }

        $metadata->setDiscriminatorMap($this->discriminatorMap);
    }
}
