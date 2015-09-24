<?php

namespace Sylius\Component\Metadata\Renderer\Custom;

use Sylius\Component\Metadata\Model\Custom\PageMetadataInterface;
use Sylius\Component\Metadata\Model\MetadataInterface;
use Sylius\Component\Metadata\Renderer\MetadataRendererInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class PageMetadataRenderer implements MetadataRendererInterface
{
    /**
     * @var callable[]
     */
    protected $subrenderers = [];

    /**
     * @var MetadataRendererInterface
     */
    protected $universalRenderer;

    /**
     * @param MetadataRendererInterface $universalRenderer
     */
    public function __construct(MetadataRendererInterface $universalRenderer)
    {
        $this->universalRenderer = $universalRenderer;

        $this->addSubrenderer('title', function ($value) {
            return sprintf('<title>%s</title>', $value);
        });

        $this->addSubrenderer(['description', 'author'], function ($value, $key) {
            return sprintf('<meta name="%s" content="%s" />', $key, $value);
        });

        $this->addSubrenderer('keywords', function ($value) {
            return sprintf('<meta name="keywords" content="%s" />', join(', ', $value));
        });

        $this->addSubrenderer('twitter', function ($value) {
            return $this->universalRenderer->render($value);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function render(MetadataInterface $metadata)
    {
        $properties = $metadata->toArray();

        $renderedProperties = [];
        foreach ($properties as $propertyKey => $propertyValue) {
            if (null === $propertyValue) {
                continue;
            }

            $this->ensurePropertyIsKnown($metadata, $propertyKey);

            $renderedProperties[] = $this->renderProperty($propertyKey, $propertyValue);
        }

        return join("\n", $renderedProperties);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(MetadataInterface $metadata)
    {
        return $metadata instanceof PageMetadataInterface;
    }

    /**
     * @param string $propertyKey
     * @param string $propertyValue
     *
     * @return string
     */
    protected function renderProperty($propertyKey, $propertyValue)
    {
        return $this->subrenderers[$propertyKey]($propertyValue, $propertyKey);
    }

    /**
     * @param string|string[] $propertyKeys
     * @param callable $subrenderer
     */
    protected function addSubrenderer($propertyKeys, callable $subrenderer)
    {
        if (!is_array($propertyKeys)) {
            $propertyKeys = [$propertyKeys];
        }

        foreach ($propertyKeys as $propertyKey) {
            $this->subrenderers[$propertyKey] = $subrenderer;
        }
    }

    /**
     * @param MetadataInterface $metadata
     * @param string $propertyKey
     *
     * @throws \InvalidArgumentException If given property is unknown
     */
    protected function ensurePropertyIsKnown(MetadataInterface $metadata, $propertyKey)
    {
        if (!isset($this->subrenderers[$propertyKey])) {
            throw new \InvalidArgumentException(sprintf(
                'Unsupported property %s::%s',
                get_class($metadata),
                $propertyKey
            ));
        }
    }
}