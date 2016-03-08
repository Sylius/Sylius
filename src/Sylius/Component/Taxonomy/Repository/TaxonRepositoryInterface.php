<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Taxonomy\Repository;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @author Anna Walasek <anna.walasek@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface TaxonRepositoryInterface extends RepositoryInterface
{
    /**
     * @param TaxonInterface $taxon
     *
     * @return TaxonInterface[]
     */
    public function findChildren(TaxonInterface $taxon);

    /**
     * @return TaxonInterface[]
     */
    public function findRootNodes();

    /**
     * @param string $permalink
     *
     * @return TaxonInterface|null
     */
    public function findOneByPermalink($permalink);

    /**
     * @param string $name
     *
     * @return TaxonInterface|null
     */
    public function findOneByName($name);
}
