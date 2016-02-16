<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReviewBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Review\Model\ReviewInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class ReviewRepository extends EntityRepository
{
    /**
     * @param int $reviewSubjectId
     *
     * @return ReviewInterface[]
     */
    public function findAcceptedBySubjectId($reviewSubjectId)
    {
        return $this
            ->getCollectionQueryBuilder()
            ->andWhere('o.reviewSubject = :reviewSubject')
            ->andWhere('o.status = :status')
            ->setParameter('reviewSubject', $reviewSubjectId)
            ->setParameter('status', ReviewInterface::STATUS_ACCEPTED)
            ->getQuery()
            ->getResult()
        ;
    }
}
