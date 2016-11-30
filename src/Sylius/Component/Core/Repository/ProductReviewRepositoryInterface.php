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

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Review\Model\ReviewInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface ProductReviewRepositoryInterface extends RepositoryInterface
{
    /**
     * @param int $count
     * @param int $productId
     *
     * @return ReviewInterface[]
     */
    public function findLatestByProductId($count, $productId);

    /**
     * @param string $slug
     * @param string $locale
     * @param ChannelInterface $channel
     *
     * @return ReviewInterface[]
     */
    public function findAcceptedByProductSlugAndChannel($slug, $locale, ChannelInterface $channel);
}
