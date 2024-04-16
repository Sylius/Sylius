<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\ApiPlatform\Metadata;

use ApiPlatform\Core\Metadata\Extractor\XmlExtractor;
use ApiPlatform\Exception\InvalidArgumentException;
use ApiPlatform\Metadata\Extractor\AbstractResourceExtractor;
use ApiPlatform\Metadata\Extractor\PropertyExtractorInterface;
use ApiPlatform\Metadata\Extractor\XmlPropertyExtractor;
use ApiPlatform\Metadata\Extractor\XmlResourceExtractor;
use Psr\Container\ContainerInterface;
use Sylius\Bundle\ApiBundle\ApiPlatform\Metadata\Merger\MetadataMergerInterface;
use Symfony\Component\Config\Util\XmlUtils;

/**
 * @see XmlExtractor
 */
final class MergingXmlExtractor extends AbstractResourceExtractor implements PropertyExtractorInterface
{
    private array $properties = [];

    /**
     * @param string[] $paths
     */
    public function __construct(
        array $paths,
        ?ContainerInterface $container = null,
        private ?MetadataMergerInterface $merger = null,
    ) {
        parent::__construct($paths, $container);
    }

    /**
     * @inheritdoc
     */
    public function getResources(): array
    {
        if (!empty($this->resources)) {
            return $this->resources;
        }

        $this->resources = [];
        foreach ($this->paths as $path) {
            $this->extractPath($path);
        }

        return $this->resources;
    }

    /**
     * @inheritdoc
     */
    public function getProperties(): array
    {
        if (!empty($this->properties)) {
            return $this->properties;
        }

        $this->properties = [];
        foreach ($this->paths as $path) {
            $this->extractPath($path);
        }

        return $this->properties;
    }

    protected function extractPath(string $path): void
    {
        try {
            /** @var \SimpleXMLElement $xml */
            $xml = simplexml_import_dom(XmlUtils::loadFile($path, XmlExtractor::RESOURCE_SCHEMA));
        } catch (\InvalidArgumentException $e) {
            // Test if this is a new resource
            try {
                $xml = XmlUtils::loadFile($path, XmlResourceExtractor::SCHEMA);

                return;
            } catch (\InvalidArgumentException) {
                try {
                    $xml = XmlUtils::loadFile($path, XmlPropertyExtractor::SCHEMA);

                    return;
                } catch (\InvalidArgumentException) {
                    throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
                }
            }
        }

        foreach ($xml->resource as $resource) {
            $resourceClass = $this->resolve((string) $resource['class']);
            $resourceMetadata = $this->buildResource($resource);

            if (null !== $this->merger && isset($this->resources[$resourceClass])) {
                $this->resources[$resourceClass] = $this->merger->merge(
                    $this->resources[$resourceClass],
                    $resourceMetadata,
                );
                $this->properties[$resourceClass] = array_merge(
                    $this->properties[$resourceClass],
                    $resourceMetadata['properties'],
                );

                continue;
            }

            $this->resources[$resourceClass] = $resourceMetadata;
            $this->properties[$resourceClass] = $this->resources[$resourceClass]['properties'];
        }
    }

    private function buildResource(\SimpleXMLElement $resource): array
    {
        return [
            'shortName' => $this->phpizeAttribute($resource, 'shortName', 'string'),
            'description' => $this->phpizeAttribute($resource, 'description', 'string'),
            'iri' => $this->phpizeAttribute($resource, 'iri', 'string'),
            'itemOperations' => $this->extractOperations($resource, 'itemOperation'),
            'collectionOperations' => $this->extractOperations($resource, 'collectionOperation'),
            'subresourceOperations' => $this->extractOperations($resource, 'subresourceOperation'),
            'graphql' => $this->extractOperations($resource, 'operation'),
            'attributes' => $this->extractAttributes($resource, 'attribute') ?: null,
            'properties' => $this->extractProperties($resource) ?: null,
        ];
    }

    /**
     * Returns the array containing configured operations. Returns NULL if there is no operation configuration.
     */
    private function extractOperations(\SimpleXMLElement $resource, string $operationType): ?array
    {
        $graphql = 'operation' === $operationType;
        if (!$graphql && $legacyOperations = $this->extractAttributes($resource, $operationType)) {
            trigger_deprecation(
                'api-platform/core',
                '2.1',
                'Configuring "%1$s" tags without using a parent "%1$ss" tag is deprecated since API Platform 2.1 and will not be possible anymore in API Platform 3',
                $operationType,
            );

            return $legacyOperations;
        }

        $operationsParent = $graphql ? 'graphql' : "{$operationType}s";
        if (!isset($resource->{$operationsParent})) {
            return null;
        }

        return $this->extractAttributes($resource->{$operationsParent}, $operationType, true);
    }

    /**
     * Recursively transforms an attribute structure into an associative array.
     */
    private function extractAttributes(\SimpleXMLElement $resource, string $elementName, bool $topLevel = false): array
    {
        $attributes = [];
        foreach ($resource->{$elementName} as $attribute) {
            $value = isset($attribute->attribute[0]) ? $this->extractAttributes($attribute, 'attribute') : $this->phpizeContent($attribute);
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
    private function extractProperties(\SimpleXMLElement $resource): array
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
                'attributes' => $this->extractAttributes($property, 'attribute'),
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
     */
    private function phpizeAttribute(\SimpleXMLElement $array, string $key, string $type): bool|int|string|null
    {
        if (!isset($array[$key])) {
            return null;
        }

        return match ($type) {
            'string' => (string) $array[$key],
            'integer' => (int) $array[$key],
            'bool' => (bool) XmlUtils::phpize($array[$key]),
            default => null,
        };
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
