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

use Sylius\Component\Resource\Model\NullDetectableInterface;

/**
 * @author Pete Ward <peter.ward@reiss.com>
 */
interface MetadataContainerTranslationInterface extends NullDetectableInterface
{
    /**
     * @return int
     */
    public function getId();
    
    /**
     * @return MetadataInterface
     */
    public function getMetadata();

    /**
     * @param MetadataInterface $metadata
     */
    public function setMetadata(MetadataInterface $metadata);
}