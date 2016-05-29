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
    public function createListQueryBuilder();

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
     *
     * @return PagerfantaInterface
     */
    public function createFilterPaginator(array $criteria = null, array $sorting = null);

    /**
     * @param int $id
     *
     * @return null|ProductInterface
     */
    public function findForDetailsPage($id);

    /**
     * @param ChannelInterface $channel
     * @param int $count
     *
     * @return ProductInterface[]
     */
    public function findLatestByChannel(ChannelInterface $channel, $count);

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

    /**
     * @param string $code
     * @param ChannelInterface $channel
     * 
     * @return ProductInterface[]|null
     */
    public function findEnabledByTaxonCodeAndChannel($code, ChannelInterface $channel);
}
