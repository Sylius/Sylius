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

namespace Sylius\Component\Promotion\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface PromotionCouponRepositoryInterface extends RepositoryInterface
{
    public function createQueryBuilderByPromotionId($promotionId): QueryBuilder;

    public function countByCodeLength(int $codeLength): int;

    public function findOneByCodeAndPromotionCode(string $code, string $promotionCode): ?PromotionCouponInterface;

    public function createPaginatorForPromotion(string $promotionCode): iterable;
}
