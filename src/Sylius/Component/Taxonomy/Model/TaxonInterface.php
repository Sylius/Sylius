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
use Sylius\Component\Resource\Model\CodeAwareInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface TaxonInterface extends CodeAwareInterface, TaxonTranslationInterface, TranslatableInterface
{
    /**
     * @return bool
     */
    public function isRoot();

    /**
     * @return TaxonInterface
     */
    public function getRoot();

    /**
     * @return TaxonInterface
     */
    public function getParent();

    /**
     * @param null|TaxonInterface $taxon
     */
    public function setParent(TaxonInterface $taxon = null);

    /**
     * @return TaxonInterface[]
     */
    public function getParents();

    /**
     * @return Collection|TaxonInterface[]
     */
    public function getChildren();

    /**
     * @param TaxonInterface $taxon
     *
     * @return bool
     */
    public function hasChild(TaxonInterface $taxon);

    /**
     * @return bool
     */
    public function hasChildren();

    /**
     * @param TaxonInterface $taxon
     */
    public function addChild(TaxonInterface $taxon);

    /**
     * @param TaxonInterface $taxon
     */
    public function removeChild(TaxonInterface $taxon);

    /**
     * @return int
     */
    public function getLeft();

    /**
     * @param int $left
     */
    public function setLeft($left);

    /**
     * @return int
     */
    public function getRight();

    /**
     * @param int $right
     */
    public function setRight($right);

    /**
     * @return int
     */
    public function getLevel();

    /**
     * @param int $level
     */
    public function setLevel($level);

    /**
     * @return int
     */
    public function getPosition();

    /**
     * @param int $position
     */
    public function setPosition($position);
}
