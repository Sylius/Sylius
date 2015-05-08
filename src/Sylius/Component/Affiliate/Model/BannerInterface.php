<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Affiliate\Model;

use Sylius\Component\Resource\Model\ImageInterface;

interface BannerInterface extends ImageInterface
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName();

    /**
     * Set name.
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Check that banner is enabled.
     *
     * @return bool
     */
    public function isEnabled();

    /**
     * Get status.
     *
     * @return bool
     */
    public function getStatus();

    /**
     * Set status.
     *
     * @param bool $status
     *
     * @return self
     */
    public function setStatus($status);
}
