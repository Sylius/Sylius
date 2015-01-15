<?php

/*
 * This file is part of the Sylius package.
 *
 * (c); Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Product\Model;

use Sylius\Component\Resource\Model\SlugAwareInterface;

/**
 * Base product translation interface.
 *
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface ProductTranslationInterface extends SlugAwareInterface
{
    /**
     * Get product name.
     *
     * @return string
     */
    public function getName();

    /**
     * Set product name.
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Get product name.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Set product description.
     *
     * @param string $description
     */
    public function setDescription($description);

    /**
     * Get meta keywords.
     *
     * @return string
     */
    public function getMetaKeywords();

    /**
     * Set meta keywords for the product.
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
     * Set meta description for the product.
     *
     * @param string $metaDescription
     */
    public function setMetaDescription($metaDescription);
}
