<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Promotion\Repository\CouponRepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class CouponRepository extends EntityRepository implements CouponRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function countCouponsByCodeLength($codeLength)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $count = (int) $queryBuilder->select($queryBuilder->expr()->count('o'))
            ->where($queryBuilder->expr()->eq($queryBuilder->expr()->length('o.code'), $codeLength))
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return $count;
    }

    /**
     * {@inheritdoc}
     */
    public function createQueryBuilderWithPromotion($promotionId)
    {
        return $this->createQueryBuilder('o')
            ->where('o.promotion = :promotionId')
            ->setParameter('promotionId', $promotionId)
        ;
    }
}
