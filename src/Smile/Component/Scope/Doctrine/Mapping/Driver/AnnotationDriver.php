<?php

namespace Smile\Component\Scope\Doctrine\Mapping\Driver;

use Doctrine\Common\Annotations\Reader;
use Metadata\Driver\DriverInterface;
use Smile\Component\Scope\Doctrine\Mapping\PropertyMetadata;
use Smile\Component\Scope\Doctrine\Mapping\ScopeAwareMetadata;
use Smile\Component\Scope\Doctrine\Mapping\ScopedValueMetadata;

class AnnotationDriver implements DriverInterface
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * Constructor
     *
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        if ($class->implementsInterface('Smile\Component\Scope\ScopeAwareInterface')) {
            return $this->loadScopeAwareMetadata($class);
        }

        if ($class->implementsInterface('Smile\Component\Scope\ScopedValueInterface')) {
            return $this->loadScopedValueMetadata($class);
        }
    }

    /**
     * Load metadata for a scope aware class
     *
     * @param \ReflectionClass $class
     * @return ScopeAwareMetadata
     */
    private function loadScopeAwareMetadata(\ReflectionClass $class)
    {
        $classMetadata = new ScopeAwareMetadata($class->name);

        foreach ($class->getProperties() as $property) {
            if ($property->class !== $class->name) {
                continue;
            }

            $propertyMetadata = new PropertyMetadata($class->name, $property->getName());

            if ($annotation = $this->reader->getPropertyAnnotation(
                $property,
                'Smile\Component\Scope\Doctrine\Annotation\ScopedValue'
            )
            ) {
                $classMetadata->targetEntity = $annotation->targetEntity;
                $classMetadata->scopedValues = $propertyMetadata;
                $classMetadata->addPropertyMetadata($propertyMetadata);
            }
        }

        return $classMetadata;
    }

    /**
     * Load metadata for a scoped value class
     *
     * @param \ReflectionClass $class
     * @return ScopedValueMetadata
     */
    private function loadScopedValueMetadata(\ReflectionClass $class)
    {
        $classMetadata = new ScopedValueMetadata($class->name);

        foreach ($class->getProperties() as $property) {
            if ($property->class !== $class->name) {
                continue;
            }

            $propertyMetadata = new PropertyMetadata($class->name, $property->getName());

            if ($annotation = $this->reader->getPropertyAnnotation(
                $property,
                'Smile\Component\Scope\Doctrine\Annotation\ScopeAware'
            )
            ) {
                $classMetadata->targetEntity = $annotation->targetEntity;
                $classMetadata->scopeAware = $annotation->scopeAware;
                $classMetadata->addPropertyMetadata($propertyMetadata);
            }

            if ($this->reader->getPropertyAnnotation($property, 'Smile\Component\Scope\Doctrine\Annotation\Scope')) {
                $classMetadata->scope = $propertyMetadata;
                $classMetadata->addPropertyMetadata($propertyMetadata);
            }
        }

        return $classMetadata;
    }
}