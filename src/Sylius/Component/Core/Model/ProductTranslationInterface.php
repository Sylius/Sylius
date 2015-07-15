<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface ProductTranslationInterface
{
    /**
     * Get product short description.
     *
     * @return string
     */
    public function getShortDescription();

    /**
     * Set product short description.
     *
     * @param string $shortDescription
     */
    public function setShortDescription($shortDescription);
}
