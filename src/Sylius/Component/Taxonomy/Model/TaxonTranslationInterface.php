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

use Sylius\Component\Resource\Model\SlugAwareInterface;

/**
 * Interface for taxon translations.
 *
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface TaxonTranslationInterface extends SlugAwareInterface
{
    /**
     * Get taxon name.
     *
     * @return string
     */
    public function getName();

    /**
     * Set taxon name.
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Get permalink.
     *
     * @return string
     */
    public function getPermalink();

    /**
     * Set permalink.
     *
     * @param string $permalink
     */
    public function setPermalink($permalink);

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Set description.
     *
     * @param string $description
     */
    public function setDescription($description);
}
