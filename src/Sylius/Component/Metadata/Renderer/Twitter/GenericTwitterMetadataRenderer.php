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

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class GenericTwitterMetadataRenderer implements MetadataRendererInterface
{
    /**
     * @var string[]
     */
    protected $keysToNames = [
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

            $renderedProperties[] = sprintf(
                '<meta name="%s" content="%s" />',
                $this->keysToNames[$propertyKey],
                htmlentities($propertyValue, \ENT_COMPAT)
            );
        }

        return join("\n", $renderedProperties);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(MetadataInterface $metadata)
    {
        return $metadata instanceof CardInterface;
    }

    /**
     * @param MetadataInterface $metadata
     * @param string $propertyKey
     */
    protected function ensurePropertyIsKnown(MetadataInterface $metadata, $propertyKey)
    {
        if (!isset($this->keysToNames[$propertyKey])) {
            throw new \InvalidArgumentException(sprintf(
                'Unsupported property %s::%s',
                get_class($metadata),
                $propertyKey
            ));
        }
    }
}
