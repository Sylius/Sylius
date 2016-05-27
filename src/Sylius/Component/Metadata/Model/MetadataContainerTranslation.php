<?php
/**
 * @author    Pete Ward <peter.ward@reiss.com>
 * @date      26/05/2016
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Metadata\Model;

use Sylius\Component\Resource\Model\AbstractTranslation;

/**
 * @author Pete Ward <peter.ward@reiss.com>
 */
class MetadataContainerTranslation extends AbstractTranslation implements MetadataContainerTranslationInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var MetadataInterface
     */
    protected $metadata;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetadata(MetadataInterface $metadata)
    {
        // Avoid saving empty translations
        if (!$metadata->isEmpty()) {
            $this->metadata = $metadata;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isNull()
    {
        return null === $this->getMetadata();
    }
}