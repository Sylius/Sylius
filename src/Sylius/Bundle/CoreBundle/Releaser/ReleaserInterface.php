<?php

/*
 * This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\CoreBundle\Releaser;

/**
 * Interface for the expired orders releaser.
 *
 * @author Foo Pang <foo.pang@gmail.com>
 */
interface ReleaserInterface
{
    /**
     * Release all expired orders.
     *
     * @param  \DateTime $expiresAt
     * @return Boolean
     */
    public function release(\DateTime $expiresAt);
}
