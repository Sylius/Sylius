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

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Review\Model\ReviewInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface ProductReviewRepositoryInterface extends RepositoryInterface
{
    /**
     * @param mixed $productId
     * @param int $count
     *
     * @return array|ReviewInterface[]
     */
    public function findLatestByProductId($productId, int $count): array;

    /**
     * @param string $slug
     * @param string $locale
     * @param ChannelInterface $channel
     *
     * @return array|ReviewInterface[]
     */
    public function findAcceptedByProductSlugAndChannel(string $slug, string $locale, ChannelInterface $channel): array;
}
