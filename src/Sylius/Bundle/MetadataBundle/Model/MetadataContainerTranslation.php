<?php
/**
 * @author    Pete Ward <peter.ward@reiss.com>
 * @date      26/05/2016
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MetadataBundle\Model;

use Sylius\Component\Metadata\Model\MetadataContainerTranslation as BaseMetadataContainerTranslation;
use Sylius\Component\Metadata\Model\MetadataInterface;

/**
 * @author Pete Ward <peter.ward@reiss.com>
 */
class MetadataContainerTranslation extends BaseMetadataContainerTranslation
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
        // Avoid saving empty translations
        if ($metadata->isEmpty()) {
            $this->metadataAsObject = null;
            $this->metadata = null;

            return;
        }

        $this->metadataAsObject = $metadata;
        $this->metadata = serialize($metadata);
    }
}