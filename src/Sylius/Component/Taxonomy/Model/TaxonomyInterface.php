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
use Sylius\Component\Translation\Model\TranslatableInterface;

/**
 * Taxonomy model interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface TaxonomyInterface extends GetIdInterface, TranslatableInterface, TaxonomyTranslationInterface, TaxonsAwareInterface
{
    /**
     * Get root taxon.
     *
     * @return TaxonInterface
     */
    public function getRoot();

    /**
     * Set root taxon.
     *
     * @param TaxonInterface $root
     */
    public function setRoot(TaxonInterface $root);

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
