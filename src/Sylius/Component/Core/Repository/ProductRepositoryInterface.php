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
use Sylius\Component\Product\Repository\ProductRepositoryInterface as BaseProductRepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface ProductRepositoryInterface extends BaseProductRepositoryInterface
{
    /**
     * @param string $locale
     *
     * @return QueryBuilder
     */
    public function createListQueryBuilder($locale);

    /**
     * @param ChannelInterface $channel
     * @param int $count
     *
     * @return ProductInterface[]
     */
    public function findLatestByChannel(ChannelInterface $channel, $count);

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

    /**
     * @param string $slug
     * @param ChannelInterface $channel
     *
     * @return ProductInterface|null
     */
    public function findOneBySlugAndChannel($slug, ChannelInterface $channel);
}
