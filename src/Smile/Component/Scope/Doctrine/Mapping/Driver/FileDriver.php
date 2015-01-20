<?php

namespace Smile\Component\Scope\Doctrine\Mapping\Driver;


use Doctrine\Common\Persistence\Mapping\Driver\FileLocator;
use Metadata\Driver\DriverInterface;
use Smile\Component\Scope\Doctrine\Mapping\MappingException;
use Smile\Component\Scope\Doctrine\Mapping\ScopeAwareMetadata;
use Smile\Component\Scope\Doctrine\Mapping\ScopedValueMetadata;

abstract class FileDriver implements DriverInterface
{
    public function __construct(FileLocator $locator)
    {
        $this->locator = $locator;
    }

    /**
     * @param \ReflectionClass $class
     *
     * @return \Metadata\ClassMetadata
     */
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        if ($class->implementsInterface('Smile\Component\Scope\ScopeAwareInterface')) {
            return $this->loadScopeAwareMetadata($class->name, $this->readMapping($class->name));
        }

        if ($class->implementsInterface('Smile\Component\Scope\ScopedValueInterface')) {
            return $this->loadScopedValueMetadata($class->name, $this->readMapping($class->name));
        }
    }

    /**
     * Get mapping filename for the given classname
     *
     * @param string $className
     * @return string|null
     */
    protected function getMappingFile($className)
    {
        try {
            return $this->locator->findMappingFile($className);
        } catch (MappingException $e) {
        }

        return null;
    }

    /**
     * Load metadata for a scopable class
     *
     * @param string $className
     * @param mixed  $config
     * @return ScopeAwareMetadata|null
     */
    abstract protected function loadScopeAwareMetadata($className, $config);

    /**
     * Load metadata for a scoped class
     *
     * @param string $className
     * @param mixed  $config
     * @return ScopedValueMetadata|null
     */
    abstract protected function loadScopedValueMetadata($className, $config);

    /**
     * Parses the given mapping file
     *
     * @param string $file
     * @return mixed
     */
    abstract protected function parse($file);

    private function readMapping($className)
    {
        $file = $this->getMappingFile($className);

        return $file ? $this->parse($file) : null;
    }
}