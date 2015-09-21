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

use Sylius\Component\Translation\Model\TranslatableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface TaxonomyInterface extends TranslatableInterface, TaxonomyTranslationInterface, TaxonsAwareInterface
{
    /**
     * @return mixed
     */
    public function getId();

    /**
     * @return TaxonInterface
     */
    public function getRoot();

    /**
     * @param TaxonInterface $root
     */
    public function setRoot(TaxonInterface $root);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);
}
