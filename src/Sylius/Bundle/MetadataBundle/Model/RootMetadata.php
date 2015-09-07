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
     * {@inheritdoc}
     */
    public function getMetadata()
    {
        return unserialize($this->metadata) ?: null;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetadata(MetadataInterface $metadata)
    {
        $this->metadata = serialize($metadata);
    }
}
