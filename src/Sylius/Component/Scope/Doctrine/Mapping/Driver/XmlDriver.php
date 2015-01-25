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


use Metadata\PropertyMetadata;
use Sylius\Component\Scope\Doctrine\Mapping\ScopeAwareMetadata;
use Sylius\Component\Scope\Doctrine\Mapping\ScopedValueMetadata;

/**
 * @author Matthieu Blottière <matthieu.blottiere@smile.fr>
 */
class XmlDriver extends FileDriver
{
    /**
     * {@inheritdoc}
     */
    protected function loadScopeAwareMetadata($className, $config)
    {
        if (!$config) {
            return null;
        }

        $xml = new \SimpleXMLElement($config);

        $xml->registerXPathNamespace('scoped', 'scoped');

        $nodeList = $xml->xpath('//scoped:scope-aware');
        if (count($nodeList) == 0) {
            return null;
        }

        if (count($nodeList) > 1) {
            throw new \Exception("Configuration defined twice");
        }

        $node = $nodeList[0];

        $classMetadata = new ScopeAwareMetadata($className);

        $scopeAwareField = (string)$node['field'];

        $propertyMetadata = new PropertyMetadata(
            $className,
            !empty($scopeAwareField) ? $scopeAwareField : 'scopedValues'
        );

        $targetEntity = $className . 'Scoped';

        $scopeAwareTargetEntity = (string)$node['target-entity'];
        $classMetadata->targetEntity = !empty($scopeAwareTargetEntity) ? $scopeAwareTargetEntity : $targetEntity;
        $classMetadata->scopedValues = $propertyMetadata;
        $classMetadata->addPropertyMetadata($propertyMetadata);

        return $classMetadata;
    }

    /**
     * {@inheritdoc}
     */
    protected function loadScopedValueMetadata($className, $config)
    {
        if (!$config) {
            return null;
        }

        $xml = new \SimpleXMLElement($config);
        $xml->registerXPathNamespace('scoped', 'scoped');
        $nodeList = $xml->xpath('//scoped:scope-aware');

        if (count($nodeList) == 0) {
            return null;
        }

        if (count($nodeList) > 1) {
            throw new \Exception("Configuration defined twice");
        }

        $node = $nodeList[0];

        $scopeAwareField = (string)$node['field'];

        $scopeAwareTargetEntity = (string)$node['target-entity'];

        $scope = (string)$node['scope'];

        $classMetadata = new ScopedValueMetadata($className);

        $propertyMetadata = new PropertyMetadata(
            $className,
            !empty($scopeAwareField) ? $scopeAwareField : 'scopeAware'
        );

        $targetEntity = 'Scoped' === substr($className, -6) ? substr($className, 0, -6) : null;

        $classMetadata->targetEntity = !empty($scopeAwareTargetEntity) ? $scopeAwareTargetEntity : $targetEntity;
        $classMetadata->scopeAware = $propertyMetadata;
        $classMetadata->addPropertyMetadata($propertyMetadata);

        if ($scope) {
            $propertyMetadata = new PropertyMetadata($className, $scope);
            $classMetadata->scope = $propertyMetadata;
            $classMetadata->addPropertyMetadata($propertyMetadata);
        }

        // Set to default if no scope property set
        if (!$classMetadata->scope) {
            $propertyMetadata = new PropertyMetadata($className, 'scope');
            $classMetadata->scope = $propertyMetadata;
            $classMetadata->addPropertyMetadata($propertyMetadata);
        }

        return $classMetadata;
    }

    /**
     * {@inheritdoc}
     */
    protected function parse($file)
    {
        return file_get_contents($file);
    }
}