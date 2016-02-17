<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Metadata\Renderer\Twitter;

use Sylius\Component\Metadata\Model\MetadataInterface;
use Sylius\Component\Metadata\Model\Twitter\CardInterface;
use Sylius\Component\Metadata\Renderer\MetadataRendererInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class GenericTwitterMetadataRenderer implements MetadataRendererInterface
{
    /**
     * @var string[]
     */
    private $keysToNames = [
        'type' => 'twitter:card',
        'site' => 'twitter:site',
        'siteId' => 'twitter:site:id',
        'creator' => 'twitter:creator',
        'creatorId' => 'twitter:creator:id',
        'title' => 'twitter:title',
        'description' => 'twitter:description',
        'image' => 'twitter:image',
        'player' => 'twitter:player',
        'playerWidth' => 'twitter:player:width',
        'playerHeight' => 'twitter:player:height',
        'playerStream' => 'twitter:player:stream',
        'playerStreamContentType' => 'twitter:player:stream:content_type',
        'appNameIphone' => 'twitter:app:name:iphone',
        'appIdIphone' => 'twitter:app:id:iphone',
        'appUrlIphone' => 'twitter:app:url:iphone',
        'appNameIpad' => 'twitter:app:name:ipad',
        'appIdIpad' => 'twitter:app:id:ipad',
        'appUrlIpad' => 'twitter:app:url:ipad',
        'appNameGooglePlay' => 'twitter:app:name:googleplay',
        'appIdGooglePlay' => 'twitter:app:id:googleplay',
        'appUrlGooglePlay' => 'twitter:app:url:googleplay',
    ];

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    /**
     * @param OptionsResolver $optionsResolver
     */
    public function __construct(OptionsResolver $optionsResolver)
    {
        $this->optionsResolver = $this->configureOptionsResolver($optionsResolver);
    }

    /**
     * {@inheritdoc}
     */
    public function render(MetadataInterface $metadata, array $options = [])
    {
        return implode(PHP_EOL, $this->renderProperties(
            $metadata,
            $this->optionsResolver->resolve($options)
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function supports(MetadataInterface $metadata, array $options = [])
    {
        return $metadata instanceof CardInterface;
    }

    /**
     * @param MetadataInterface $metadata
     * @param string $propertyKey
     */
    private function ensurePropertyIsKnown(MetadataInterface $metadata, $propertyKey)
    {
        if (!isset($this->keysToNames[$propertyKey])) {
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
    private function renderProperties(MetadataInterface $metadata, array $options)
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

            $renderedProperties[] = sprintf(
                '<meta name="%s" content="%s" />',
                $this->keysToNames[$propertyKey],
                htmlentities($propertyValue, \ENT_COMPAT)
            );
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
