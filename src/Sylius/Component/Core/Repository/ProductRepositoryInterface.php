<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Product\Repository\ProductRepositoryInterface as BaseProductRepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface ProductRepositoryInterface extends BaseProductRepositoryInterface
{
    /**
     * @param string $locale
     * @param mixed|null $taxonId
     *
     * @return QueryBuilder
     */
    public function createListQueryBuilder(string $locale, $taxonId = null): QueryBuilder;

    /**
     * @param ChannelInterface $channel
     * @param TaxonInterface $taxon
     * @param string $locale
     * @param array $sorting
     *
     * @return QueryBuilder
     */
    public function createShopListQueryBuilder(ChannelInterface $channel, TaxonInterface $taxon, string $locale, array $sorting = []): QueryBuilder;

    /**
     * @param ChannelInterface $channel
     * @param string $locale
     * @param string $count
     *
     * @return array|ProductInterface[]
     */
    public function findLatestByChannel(ChannelInterface $channel, string $locale, string $count): array;

    /**
     * @param ChannelInterface $channel
     * @param string $locale
     * @param string $slug
     *
     * @return ProductInterface|null
     */
    public function findOneByChannelAndSlug(ChannelInterface $channel, string $locale, string $slug): ?ProductInterface;

    /**
     * @param string $code
     *
     * @return ProductInterface|null
     */
    public function findOneByCode(string $code): ?ProductInterface;
}
