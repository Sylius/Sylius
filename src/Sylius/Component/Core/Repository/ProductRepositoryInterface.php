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
 * @template T of ProductInterface
 *
 * @extends BaseProductRepositoryInterface<T>
 */
interface ProductRepositoryInterface extends BaseProductRepositoryInterface
{
    /** @param mixed|null $taxonId */
    public function createListQueryBuilder(string $locale, $taxonId = null): QueryBuilder;

    public function createShopListQueryBuilder(
        ChannelInterface $channel,
        TaxonInterface $taxon,
        string $locale,
        array $sorting = [],
        bool $includeAllDescendants = false,
    ): QueryBuilder;

    /**
     * @return array|ProductInterface[]
     */
    public function findLatestByChannel(ChannelInterface $channel, string $locale, int $count): array;

    public function findOneByChannelAndSlug(ChannelInterface $channel, string $locale, string $slug): ?ProductInterface;

    public function findOneByChannelAndCode(ChannelInterface $channel, string $code): ?ProductInterface;

    public function findOneByCode(string $code): ?ProductInterface;

    public function findByTaxon(TaxonInterface $taxon): array;
}
