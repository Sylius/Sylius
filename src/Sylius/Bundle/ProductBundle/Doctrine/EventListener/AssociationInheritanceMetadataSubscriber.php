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
    private $discriminatorMap = [];

    public function __construct($discriminatorMap)
    {
        $this->discriminatorMap = $discriminatorMap;
    }

    public function getSubscribedEvents()
    {
        return ['loadClassMetadata'];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        /**
         * @var \Doctrine\ORM\Mapping\ClassMetadata|\Doctrine\ODM\MongoDB\Mapping\ClassMetadata $metadata
         */
        $metadata = $eventArgs->getClassMetadata();

        if ($metadata->getName() != self::ASSOCIATION_CLASS_NAME) {
            return;
        }

        $metadata->setDiscriminatorMap($this->discriminatorMap);
    }
}
