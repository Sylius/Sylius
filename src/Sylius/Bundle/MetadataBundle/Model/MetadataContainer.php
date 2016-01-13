<?php

namespace Sylius\Bundle\MetadataBundle\Model;

use Sylius\Component\Metadata\Model\MetadataInterface;
use Sylius\Component\Metadata\Model\MetadataContainer as BaseMetadataContainer;
use Sylius\Component\Metadata\Model\MetadataContainerInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class MetadataContainer extends BaseMetadataContainer implements MetadataContainerInterface
{
    /**
     * @var MetadataInterface
     */
    protected $metadataAsObject;

    /**
     * {@inheritdoc}
     */
    public function getMetadata()
    {
        if (null !== $this->metadataAsObject) {
            return $this->metadataAsObject;
        }

        return unserialize($this->metadata) ?: null;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetadata(MetadataInterface $metadata)
    {
        $this->metadataAsObject = $metadata;
    }

    public function serializeMetadata()
    {
        $this->metadata = serialize($this->metadataAsObject);
    }
}
