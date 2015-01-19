<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Model;

interface SeoExtraInterface
{
    /**
     * Get SEO extras.
     *
     * @return array
     */
    public function getSeoExtra();

    /**
     * Set SEO extras.
     *
     * @param array $extra
     */
    public function setSeoExtra(array $extra = null);
}
