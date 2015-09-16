<?php

namespace Sylius\Component\Metadata\Renderer\Custom;

use Sylius\Component\Metadata\Model\Custom\PageMetadataInterface;
use Sylius\Component\Metadata\Model\MetadataInterface;
use Sylius\Component\Metadata\Renderer\MetadataRendererInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
     * @var OptionsResolver
     */
    protected $optionsResolver;

    /**
     * @param MetadataRendererInterface $universalRenderer
     * @param OptionsResolver $optionsResolver
     */
    public function __construct(MetadataRendererInterface $universalRenderer, OptionsResolver $optionsResolver)
    {
        $this->universalRenderer = $universalRenderer;

        $this->optionsResolver = $this->configureOptionsResolver($optionsResolver);

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
    public function render(MetadataInterface $metadata, array $options = [])
    {
        return join("\n", $this->renderProperties(
            $metadata,
            $this->optionsResolver->resolve($options)
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function supports(MetadataInterface $metadata, array $options = [])
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

    /**
     * @param MetadataInterface $metadata
     * @param array $options
     *
     * @return array
     */
    protected function renderProperties(MetadataInterface $metadata, array $options)
    {
        $properties = array_replace_recursive(
            $options['defaults'],
            array_filter($metadata->toArray(), function ($item) { return null !== $item; })
        );

        $renderedProperties = [];
        foreach ($properties as $propertyKey => $propertyValue) {
            if (null === $propertyValue) {
                continue;
            }

            $this->ensurePropertyIsKnown($metadata, $propertyKey);

            $renderedProperties[] = $this->renderProperty($propertyKey, $propertyValue);
        }

        return $renderedProperties;
    }

    /**
     * @param OptionsResolver $optionsResolver
     *
     * @return OptionsResolver
     */
    private function configureOptionsResolver(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setDefaults([
            'group' => 'head',
            'defaults' => [],
        ]);

        $optionsResolver->setAllowedValues('group', ['head']);
        $optionsResolver->setAllowedTypes('defaults', 'array');

        return $optionsResolver;
    }
}