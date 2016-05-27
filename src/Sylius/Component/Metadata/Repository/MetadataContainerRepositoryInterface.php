<?php
/**
 * @author    Pete Ward <peter.ward@reiss.com>
 * @date      25/05/2016
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Metadata\Repository;

use Sylius\Component\Metadata\Model\MetadataContainerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Pete Ward <peter.ward@reiss.com>
 */
interface MetadataContainerRepositoryInterface extends RepositoryInterface
{
    /**
     * @param string $type
     * @param string $code
     *
     * @return MetadataContainerInterface|null
     */
    public function findOneByTypeAndCode($type, $code);
}