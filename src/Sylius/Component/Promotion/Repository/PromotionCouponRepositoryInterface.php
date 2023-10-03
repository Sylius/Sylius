<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Promotion\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @template T of PromotionCouponInterface
 *
 * @extends RepositoryInterface<T>
 */
interface PromotionCouponRepositoryInterface extends RepositoryInterface
{
    public function createQueryBuilderByPromotionId($promotionId): QueryBuilder;

    public function countByCodeLength(
        int $codeLength,
        ?string $prefix = null,
        ?string $suffix = null,
    ): int;

    public function findOneByCodeAndPromotionCode(string $code, string $promotionCode): ?PromotionCouponInterface;

    public function createPaginatorForPromotion(string $promotionCode): iterable;
}
