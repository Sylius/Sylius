<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AssociationBundle\Doctrine\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LoadClassMetadataEventArgs;

/**
 * Doctrine subscriber which able to add own association object
 *
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
class AssociationInheritanceMetadataSubscriber implements EventSubscriber
{
    const ASSOCIATION_CLASS_NAME = 'Sylius\Component\Association\Model\Association';

    /**
     * @var array
     */
    private $discriminatorMap = array();

    /**
     * @param array $discriminatorMap
     */
    public function __construct(array $discriminatorMap)
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

    /**
     * @param LoadClassMetadataEventArgs $eventArguments
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArguments)
    {
        $classMetadata = $eventArguments->getClassMetadata();

        if ($classMetadata->getName() !== self::ASSOCIATION_CLASS_NAME) {
            return;
        }

        $classMetadata->setDiscriminatorMap($this->discriminatorMap);
    }
}
