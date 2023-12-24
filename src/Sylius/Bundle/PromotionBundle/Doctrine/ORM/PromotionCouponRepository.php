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

namespace Sylius\Bundle\PromotionBundle\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;

/**
 * @template T of PromotionCouponInterface
 *
 * @implements PromotionCouponRepositoryInterface<T>
 */
class PromotionCouponRepository extends EntityRepository implements PromotionCouponRepositoryInterface
{
    public function createQueryBuilderByPromotionId($promotionId): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.promotion = :promotionId')
            ->setParameter('promotionId', $promotionId)
        ;
    }

    public function countByCodeLength(
        int $codeLength,
        ?string $prefix = null,
        ?string $suffix = null,
    ): int {
        if ($prefix !== null) {
            $codeLength += strlen($prefix);
        }
        if ($suffix !== null) {
            $codeLength += strlen($suffix);
        }
        $codeTemplate = $prefix . '%' . $suffix;

        return (int) $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->andWhere('LENGTH(o.code) = :codeLength')
            ->andWhere('o.code LIKE :codeTemplate')
            ->setParameter('codeLength', $codeLength)
            ->setParameter('codeTemplate', $codeTemplate)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function findOneByCodeAndPromotionCode(string $code, string $promotionCode): ?PromotionCouponInterface
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.promotion', 'promotion')
            ->where('promotion.code = :promotionCode')
            ->andWhere('o.code = :code')
            ->setParameter('promotionCode', $promotionCode)
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function createPaginatorForPromotion(string $promotionCode): iterable
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->leftJoin('o.promotion', 'promotion')
            ->where('promotion.code = :promotionCode')
            ->setParameter('promotionCode', $promotionCode)
        ;

        return $this->getPaginator($queryBuilder);
    }
}
