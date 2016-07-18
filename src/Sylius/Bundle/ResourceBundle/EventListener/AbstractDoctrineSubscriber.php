<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Sylius\Component\Resource\Metadata\RegistryInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @author Ben Davies <ben.davies@gmail.com>
 */
abstract class AbstractDoctrineSubscriber implements EventSubscriber
{
    /**
     * @var RegistryInterface
     */
    protected $resourceRegistry;

    /**
     * @param RegistryInterface $resourceRegistry
     */
    public function __construct(RegistryInterface $resourceRegistry)
    {
        $this->resourceRegistry = $resourceRegistry;
    }

    /**
     * @param ClassMetadata $metadata
     *
     * @return bool
     */
    protected function isSyliusClass(ClassMetadata $metadata)
    {
        if (!$reflClass = $metadata->getReflectionClass()) {
            return false;
        }

        return $reflClass->implementsInterface(ResourceInterface::class);
    }
}
