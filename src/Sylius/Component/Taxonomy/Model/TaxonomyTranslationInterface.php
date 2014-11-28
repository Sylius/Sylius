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

use Doctrine\Common\Collections\Collection;

/**
 * Taxonomy translation model interface.
 *
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface TaxonomyTranslationInterface
{
    /**
     * Get taxonomy id.
     *
     * @return mixed
     */
    public function getId();

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
