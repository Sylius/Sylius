<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Taxonomy\Model;

use Sylius\Component\Resource\Model\GetIdInterface;

/**
 * Taxonomy translation model interface.
 *
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface TaxonomyTranslationInterface extends GetIdInterface
{
    /**
     * Get name.
     *
     * @return string
     */
    public function getName();

    /**
     * Set taxonomy name.
     *
     * @param string $name
     */
    public function setName($name);
}
