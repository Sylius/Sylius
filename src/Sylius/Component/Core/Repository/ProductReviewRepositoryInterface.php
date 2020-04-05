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
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Review\Model\ReviewInterface;

interface ProductReviewRepositoryInterface extends RepositoryInterface
{
    /**
     * @return array|ReviewInterface[]
     */
    public function findLatestByProductId($productId, int $count): array;

    /**
     * @return array|ReviewInterface[]
     */
    public function findAcceptedByProductSlugAndChannel(string $slug, string $locale, ChannelInterface $channel): array;

    public function createQueryBuilderByProductCode(string $locale, string $productCode): QueryBuilder;

    public function findOneByIdAndProductCode($id, string $productCode): ?ReviewInterface;
}
