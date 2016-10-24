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
     * @param string $localeCode
     * @param mixed|null $taxonId
     *
     * @return QueryBuilder
     */
    public function createQueryBuilderWithLocaleCodeAndTaxonId($localeCode, $taxonId = null);

    /**
     * @param string $code
     * @param ChannelInterface $channel
     *
     * @return QueryBuilder
     */
    public function createQueryBuilderForEnabledByTaxonCodeAndChannel($code, ChannelInterface $channel);
    /**
     * @param mixed $id
     * @param ChannelInterface $channel
     *
     * @return ProductInterface|null
     */
    public function findOneByIdAndChannel($id, ChannelInterface $channel = null);

    /**
     * @param string $slug
     * @param ChannelInterface $channel
     *
     * @return ProductInterface|null
     */
    public function findOneBySlugAndChannel($slug, ChannelInterface $channel);

    /**
     * @param string $slug
     *
     * @return ProductInterface|null
     */
    public function findOneBySlug($slug);

    /**
     * @param ChannelInterface $channel
     * @param int $count
     *
     * @return ProductInterface[]
     */
    public function findLatestByChannel(ChannelInterface $channel, $count);

    /**
     * @param string $code
     * @param ChannelInterface $channel
     *
     * @return ProductInterface[]|null
     */
    public function findEnabledByTaxonCodeAndChannel($code, ChannelInterface $channel);
}
