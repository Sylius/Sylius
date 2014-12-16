<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Addressing\Model;

/**
 * Country translation interface.
 *
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface CountryTranslationInterface
{
    /**
     * Get country name.
     *
     * @return string
     */
    public function getName();

    /**
     * Set country name.
     *
     * @param string $name
     */
    public function setName($name);
}
