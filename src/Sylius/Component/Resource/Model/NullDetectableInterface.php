<?php
/**
 * @author    Pete Ward <peter.ward@reiss.com>
 * @date      27/05/2016
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Model;

/**
 * @author Pete Ward <peter.ward@reiss.com>
 */
interface NullDetectableInterface
{
    /**
     * @return boolean
     */
    public function isNull();
}