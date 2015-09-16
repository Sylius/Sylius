<?php

namespace Sylius\Component\Metadata\Renderer\Custom;

use Sylius\Component\Metadata\Model\Custom\PageMetadataInterface;
use Sylius\Component\Metadata\Model\MetadataInterface;
use Sylius\Component\Metadata\Renderer\MetadataRendererInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

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
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @param MetadataRendererInterface $universalRenderer
     * @param OptionsResolver $optionsResolver
     * @param PropertyAccessorInterface $propertyAccessor
     */
    public function __construct(MetadataRendererInterface $universalRenderer, OptionsResolver $optionsResolver, PropertyAccessorInterface $propertyAccessor)
    {
        $this->universalRenderer = $universalRenderer;
        $this->optionsResolver = $this->configureOptionsResolver($optionsResolver);
        $this->propertyAccessor = $propertyAccessor;

        $this->declareSubrenderers();
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
        $this->setDefaultValuesOnMetadata($metadata, $options['defaults']);

        $renderedProperties = [];
        foreach (array_keys($metadata->toArray()) as $propertyKey) {
            $propertyValue = $this->propertyAccessor->getValue($metadata, $propertyKey);

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

    protected function declareSubrenderers()
    {
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
     * @param MetadataInterface $metadata
     * @param array $defaultValues
     *
     * @return MetadataInterface
     */
    protected function setDefaultValuesOnMetadata(MetadataInterface $metadata, array $defaultValues)
    {
        foreach ($defaultValues as $propertyPath => $value) {
            if (null !== $this->propertyAccessor->getValue($metadata, $propertyPath)) {
                continue;
            }

            $this->propertyAccessor->setValue($metadata, $propertyPath, $value);
        }
    }
}