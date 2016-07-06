<?php
/**
 * @author    Pete Ward <peter.ward@reiss.com>
 * @date      25/05/2016
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Metadata\Model\Custom;

use Sylius\Component\Metadata\Model\MetadataSubjectInterface;

/**
 * @author Pete Ward <peter.ward@reiss.com>
 */
interface PageInterface extends MetadataSubjectInterface
{
    const METADATA_CLASS_IDENTIFIER = 'DefaultPage';
}