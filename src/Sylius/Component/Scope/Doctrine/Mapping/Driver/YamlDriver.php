<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Scope\Doctrine\Mapping\Driver;


use Sylius\Component\Scope\Doctrine\Mapping\PropertyMetadata;
use Sylius\Component\Scope\Doctrine\Mapping\ScopeAwareMetadata;
use Sylius\Component\Scope\Doctrine\Mapping\ScopedValueMetadata;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Matthieu Blottière <matthieu.blottiere@smile.fr>
 */
class YamlDriver extends FileDriver
{

    /**
     * {@inheritdoc}
     */
    protected function loadScopeAwareMetadata($className, $config)
    {
        if (!isset($config[$className])
            || !isset($config[$className]['scoped'])
            || !array_key_exists('scope-aware', $config[$className]['scoped'])
        ) {
            return null;
        }

        $classMetadata = new ScopeAwareMetadata($className);

        $scopeAware = $config[$className]['scoped']['scope-aware'] ?: array();

        $propertyMetadata = new PropertyMetadata(
            $className,
            isset($scopeAware['field']) ? $scopeAware['field'] : 'scopedValues'
        );

        $targetEntity = $className . 'Scoped';

        $classMetadata->targetEntity = isset($scopeAware['targetEntity']) ? $scopeAware['targetEntity'] : $targetEntity;
        $classMetadata->scopedValues = $propertyMetadata;
        $classMetadata->addPropertyMetadata($propertyMetadata);

        return $classMetadata;
    }

    /**
     * {@inheritdoc}
     */
    protected function loadScopedValueMetadata($className, $config)
    {
        if (!isset($config[$className])
            || !isset($config[$className]['scoped'])
            || !array_key_exists('scope-aware', $config[$className]['scoped'])
        ) {
            return null;
        }

        $classMetadata = new ScopedValueMetadata($className);

        $scopeAware = $config[$className]['scoped']['scope-aware'] ?: array();

        $propertyMetadata = new PropertyMetadata(
            $className,
            isset($scopeAware['field']) ? $scopeAware['field'] : 'scopeAware'
        );

        $targetEntity = 'Scoped' === substr($className, -6) ? substr($className, 0, -6) : null;

        $classMetadata->targetEntity = isset($scopeAware['targetEntity']) ? $scopeAware['targetEntity'] : $targetEntity;
        $classMetadata->scopeAware = $propertyMetadata;
        $classMetadata->addPropertyMetadata($propertyMetadata);

        $scope = isset($scopeAware['scope']) ? $scopeAware['scope'] : 'scope';
        $propertyMetadata = new PropertyMetadata($className, $scope);
        $classMetadata->scope = $propertyMetadata;
        $classMetadata->addPropertyMetadata($propertyMetadata);

        return $classMetadata;
    }

    /**
     * {@inheritdoc}
     */
    protected function parse($file)
    {
        return Yaml::parse($file);
    }
}