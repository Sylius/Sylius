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

interface SeoMetaInterface
{
    /**
     * Get meta title.
     *
     * @return string
     */
    public function getMetaTitle();

    /**
     * Set meta title.
     *
     * @param string $title
     */
    public function setMetaTitle($title);

    /**
     * Get meta keywords.
     *
     * @return string
     */
    public function getMetaKeywords();

    /**
     * Set meta keywords.
     *
     * @param string $metaKeywords
     */
    public function setMetaKeywords($metaKeywords);

    /**
     * Get meta description.
     *
     * @return string
     */
    public function getMetaDescription();

    /**
     * Set meta description.
     *
     * @param string $metaDescription
     */
    public function setMetaDescription($metaDescription);
}
