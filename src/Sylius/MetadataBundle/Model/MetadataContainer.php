<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\MetadataBundle\Model;

use Sylius\Metadata\Model\MetadataContainer as BaseMetadataContainer;
use Sylius\Metadata\Model\MetadataInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class MetadataContainer extends BaseMetadataContainer
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
        $this->metadata = serialize($metadata);
    }
}
