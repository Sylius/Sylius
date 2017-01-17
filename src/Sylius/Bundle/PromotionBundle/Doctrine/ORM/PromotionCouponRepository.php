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
use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class PromotionCouponRepository extends EntityRepository implements PromotionCouponRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createQueryBuilderByPromotionId($promotionId)
    {
        return $this->createQueryBuilder('o')
            ->where('o.promotion = :promotionId')
            ->setParameter('promotionId', $promotionId)
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function countByCodeLength($codeLength)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        return (int) $queryBuilder->select($queryBuilder->expr()->count('o'))
            ->where($queryBuilder->expr()->eq($queryBuilder->expr()->length('o.code'), $codeLength))
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
