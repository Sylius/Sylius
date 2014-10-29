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
use Sylius\Component\Core\Model\UserInterface;
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
            ->andWhere($queryBuilder->expr()->lte(
                $this->getAlias() . '.scheduledDate',
                ':now'
            ))
            ->andWhere($queryBuilder->expr()->orx(
                $queryBuilder->expr()->lt(
                    $this->getAlias() . '.processedDate',
                    $this->getAlias() . '.scheduledDate'
                ),
                $queryBuilder->expr()->isNull(
                    $this->getAlias() . '.processedDate'
                )
            ))
            ->setParameter('now', new \DateTime())
            ->orderBy($this->getAlias() . '.scheduledDate', 'asc')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findByUser(UserInterface $user)
    {
        return $this->getQueryBuilder()
            ->andWhere($this->getAlias() . '.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult()
        ;
    }

    protected function getQueryBuilder()
    {
        $queryBuilder = parent::getQueryBuilder();

        return $queryBuilder
            ->join($this->getAlias() . '.orderItem', 'i')
            ->join('i.order', 'O')
            ->andWhere($queryBuilder->expr()->isNotNull('O.completedAt'))
        ;
    }
}
