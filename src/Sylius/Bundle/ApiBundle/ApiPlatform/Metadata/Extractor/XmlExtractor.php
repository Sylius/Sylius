<?php

namespace Sylius\Bundle\ApiBundle\ApiPlatform\Metadata\Extractor;

use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Metadata\Extractor\AbstractExtractor;
use ApiPlatform\Core\Metadata\Extractor\XmlExtractor;
use Psr\Container\ContainerInterface;
use Symfony\Component\Config\Util\XmlUtils;

final class XmlExtractor extends AbstractExtractor
{
    public const RESOURCE_SCHEMA = __DIR__.'/../schema/metadata.xsd';

    /**
     * {@inheritdoc}
     */
    protected function extractPath(string $path)
    {
        try {
            /** @var \SimpleXMLElement $xml */
            $xml = simplexml_import_dom(XmlUtils::loadFile($path, self::RESOURCE_SCHEMA));
        } catch (\InvalidArgumentException $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }

        foreach ($xml->resource as $resource) {
            $resourceClass = $this->resolve((string) $resource['class']);

            $this->resources[$resourceClass] = [
                'shortName' => $this->phpizeAttribute($resource, 'shortName', 'string'),
                'description' => $this->phpizeAttribute($resource, 'description', 'string'),
                'iri' => $this->phpizeAttribute($resource, 'iri', 'string'),
                'itemOperations' => $this->getOperations($resource, 'itemOperation'),
                'collectionOperations' => $this->getOperations($resource, 'collectionOperation'),
                'subresourceOperations' => $this->getOperations($resource, 'subresourceOperation'),
                'graphql' => $this->getOperations($resource, 'operation'),
                'attributes' => $this->getAttributes($resource, 'attribute') ?: null,
                'properties' => $this->getProperties($resource) ?: null,
                'enabled' => $this->getAttributes($resource, 'enabled'),
            ];
        }
    }

    /**
     * Returns the array containing configured operations. Returns NULL if there is no operation configuration.
     */
    private function getOperations(\SimpleXMLElement $resource, string $operationType): ?array
    {
        $graphql = 'operation' === $operationType;
        if (!$graphql && $legacyOperations = $this->getAttributes($resource, $operationType)) {
            @trigger_error(
                sprintf('Configuring "%1$s" tags without using a parent "%1$ss" tag is deprecated since API Platform 2.1 and will not be possible anymore in API Platform 3', $operationType),
                \E_USER_DEPRECATED
            );

            return $legacyOperations;
        }

        $operationsParent = $graphql ? 'graphql' : "{$operationType}s";
        if (!isset($resource->{$operationsParent})) {
            return null;
        }

        return $this->getAttributes($resource->{$operationsParent}, $operationType, true);
    }

    /**
     * Recursively transforms an attribute structure into an associative array.
     */
    private function getAttributes(\SimpleXMLElement $resource, string $elementName, bool $topLevel = false): array
    {
        $attributes = [];
        foreach ($resource->{$elementName} as $attribute) {
            $value = isset($attribute->attribute[0]) ? $this->getAttributes($attribute, 'attribute') : $this->phpizeContent($attribute);
            // allow empty operations definition, like <collectionOperation name="post" />
            if ($topLevel && '' === $value) {
                $value = [];
            }
            if (isset($attribute['name'])) {
                $attributes[(string) $attribute['name']] = $value;
            } else {
                $attributes[] = $value;
            }
        }

        return $attributes;
    }

    /**
     * Gets metadata of a property.
     */
    private function getProperties(\SimpleXMLElement $resource): array
    {
        $properties = [];
        foreach ($resource->property as $property) {
            $properties[(string) $property['name']] = [
                'description' => $this->phpizeAttribute($property, 'description', 'string'),
                'readable' => $this->phpizeAttribute($property, 'readable', 'bool'),
                'writable' => $this->phpizeAttribute($property, 'writable', 'bool'),
                'readableLink' => $this->phpizeAttribute($property, 'readableLink', 'bool'),
                'writableLink' => $this->phpizeAttribute($property, 'writableLink', 'bool'),
                'required' => $this->phpizeAttribute($property, 'required', 'bool'),
                'identifier' => $this->phpizeAttribute($property, 'identifier', 'bool'),
                'iri' => $this->phpizeAttribute($property, 'iri', 'string'),
                'attributes' => $this->getAttributes($property, 'attribute'),
                'subresource' => $property->subresource ? [
                    'collection' => $this->phpizeAttribute($property->subresource, 'collection', 'bool'),
                    'resourceClass' => $this->resolve($this->phpizeAttribute($property->subresource, 'resourceClass', 'string')),
                    'maxDepth' => $this->phpizeAttribute($property->subresource, 'maxDepth', 'integer'),
                ] : null,
            ];
        }

        return $properties;
    }

    /**
     * Transforms an XML attribute's value in a PHP value.
     *
     * @return string|int|bool|null
     */
    private function phpizeAttribute(\SimpleXMLElement $array, string $key, string $type)
    {
        if (!isset($array[$key])) {
            return null;
        }

        switch ($type) {
            case 'string':
                return (string) $array[$key];
            case 'integer':
                return (int) $array[$key];
            case 'bool':
                return (bool) XmlUtils::phpize($array[$key]);
        }

        return null;
    }

    /**
     * Transforms an XML element's content in a PHP value.
     */
    private function phpizeContent(\SimpleXMLElement $array)
    {
        $type = $array['type'] ?? null;
        $value = (string) $array;

        switch ($type) {
            case 'string':
                return $value;
            case 'constant':
                return \constant($value);
            default:
                return XmlUtils::phpize($value);
        }
    }
}
