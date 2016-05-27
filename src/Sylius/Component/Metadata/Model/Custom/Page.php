<?php
/**
 * @author    Pete Ward <peter.ward@reiss.com>
 * @date      24/05/2016
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Metadata\Model\Custom;

/**
 * @author Pete Ward <peter.ward@reiss.com>
 */
class Page implements PageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getMetadataClassIdentifier()
    {
        return self::METADATA_CLASS_IDENTIFIER;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadataIdentifier()
    {
        return self::METADATA_CLASS_IDENTIFIER;
    }
}