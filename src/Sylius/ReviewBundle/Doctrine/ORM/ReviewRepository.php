<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\ReviewBundle\Doctrine\ORM;

use Sylius\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Review\Repository\ReviewRepositoryInterface;
use Sylius\Review\Model\ReviewInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class ReviewRepository extends EntityRepository implements ReviewRepositoryInterface
{
    /**
     * @param int $reviewSubjectId
     * @param int $limit
     *
     * @return ReviewInterface[]
     */
    public function findAcceptedBySubjectId($reviewSubjectId, $limit = 5)
    {
        return $this
            ->createQueryBuilder('o')
            ->andWhere('o.reviewSubject = :reviewSubject')
            ->andWhere('o.status = :status')
            ->setParameter('reviewSubject', $reviewSubjectId)
            ->setParameter('status', ReviewInterface::STATUS_ACCEPTED)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }
}
