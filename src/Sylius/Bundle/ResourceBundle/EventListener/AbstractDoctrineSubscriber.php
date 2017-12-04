<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ResourceBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\Mapping\ReflectionService;
use Doctrine\Common\Persistence\Mapping\RuntimeReflectionService;
use Sylius\Component\Resource\Metadata\RegistryInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

abstract class AbstractDoctrineSubscriber implements EventSubscriber
{
    /**
     * @var RegistryInterface
     */
    protected $resourceRegistry;

    /**
     * @var RuntimeReflectionService
     */
    private $reflectionService;

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
    protected function isResource(ClassMetadata $metadata): bool
    {
        if (!$reflClass = $metadata->getReflectionClass()) {
            return false;
        }

        return $reflClass->implementsInterface(ResourceInterface::class);
    }

    protected function getReflectionService(): ReflectionService
    {
        if ($this->reflectionService === null) {
            $this->reflectionService = new RuntimeReflectionService();
        }

        return $this->reflectionService;
    }
}
