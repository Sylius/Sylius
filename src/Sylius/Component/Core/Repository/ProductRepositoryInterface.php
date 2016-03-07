<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Repository;

use Pagerfanta\PagerfantaInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Product\Model\ArchetypeInterface;
use Sylius\Component\Product\Repository\ProductRepositoryInterface as BaseProductRepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface ProductRepositoryInterface extends BaseProductRepositoryInterface
{
    /**
     * @param TaxonInterface $taxon
     * @param array $criteria
     *
     * @return PagerfantaInterface
     */
    public function createByTaxonPaginator(TaxonInterface $taxon, array $criteria = []);

    /**
     * @param TaxonInterface $taxon
     * @param ChannelInterface $channel
     *
     * @return PagerfantaInterface
     */
    public function createByTaxonAndChannelPaginator(TaxonInterface $taxon, ChannelInterface $channel);

    /**
     * @param array $criteria
     * @param array $sorting
     * @param bool $deleted
     *
     * @return PagerfantaInterface
     */
    public function createFilterPaginator(array $criteria = null, array $sorting = null, $deleted = false);

    /**
     * Get the product data for the details page.
     *
     * @param int $id
     *
     * @return null|ProductInterface
     */
    public function findForDetailsPage($id);

    /**
     * Find X recently added products.
     *
     * @param int $limit
     * @param ChannelInterface $channel
     *
     * @return ProductInterface[]
     */
    public function findLatest($limit = 10, ChannelInterface $channel);

    /**
     * @param ArchetypeInterface $archetype
     * @param array $criteria
     *
     * @return PagerfantaInterface
     */
    public function createByProductArchetypePaginator(ArchetypeInterface $archetype, array $criteria = []);

    /**
     * @param mixed $id
     * @param ChannelInterface $channel
     *
     * @return ProductInterface|null
     */
    public function findOneByIdAndChannel($id, ChannelInterface $channel = null);
}
