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

use Doctrine\ORM\QueryBuilder;
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
     * @param string $parentCode
     * @param string $locale
     *
     * @return TaxonInterface[]
     */
    public function findChildren($parentCode, $locale);

    /**
     * @return TaxonInterface[]
     */
    public function findRootNodes();

    /**
     * @param string $slug
     * @param string $locale
     *
     * @return TaxonInterface|null
     */
    public function findOneBySlug($slug, $locale);

    /**
     * @param string $name
     * @param string $locale
     *
     * @return TaxonInterface[]
     */
    public function findByName($name, $locale);

    /**
     * @param string $phrase
     * @param string $locale
     *
     * @return TaxonInterface[]
     */
    public function findByNamePart($phrase, $locale);

    /**
     * @return QueryBuilder
     */
    public function createListQueryBuilder();
}
