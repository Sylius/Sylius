<?php

/*
 * This file is a part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MetadataBundle\Model;

use Sylius\Component\Metadata\Model\MetadataContainer as BaseMetadataContainer;
use Sylius\Component\Metadata\Model\MetadataInterface;

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
