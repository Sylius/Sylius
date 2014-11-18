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

interface SlugAwareInterface
{
    /**
     * Get permalink/slug.
     *
     * @return null|string
     */
    public function getSlug();

    /**
     * Set the permalink.
     *
     * @param null|string $slug
     */
    public function setSlug($slug = null);
}
