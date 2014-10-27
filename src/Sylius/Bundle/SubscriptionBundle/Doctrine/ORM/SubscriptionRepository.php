<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\SubscriptionBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Subscription\Repository\SubscriptionRepositoryInterface;

/**
 * Subscription repository
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class SubscriptionRepository extends EntityRepository implements SubscriptionRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findScheduled(\DateTime $date = null)
    {
        $queryBuilder = $this->getQueryBuilder();

        return $queryBuilder
            ->andWhere($queryBuilder->expr()->lte('o.scheduledDate', ':now'))
            ->andWhere(
                $queryBuilder->expr()->orx(
                    $queryBuilder->expr()->lt('o.processedDate', 'o.scheduledDate'),
                    $queryBuilder->expr()->isNull('o.processedDate')
                )
            )
            ->setParameter('now', new \DateTime())
            ->orderBy('o.scheduledDate', 'asc')
            ->getQuery()
            ->getResult()
        ;
    }
}
