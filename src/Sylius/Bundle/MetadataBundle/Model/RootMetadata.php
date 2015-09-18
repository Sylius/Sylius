<?php

namespace Sylius\Bundle\MetadataBundle\Model;

use Sylius\Component\Metadata\Model\MetadataInterface;
use Sylius\Component\Metadata\Model\RootMetadata as BaseRootMetadata;
use Sylius\Component\Metadata\Model\RootMetadataInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class RootMetadata extends BaseRootMetadata implements RootMetadataInterface
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
