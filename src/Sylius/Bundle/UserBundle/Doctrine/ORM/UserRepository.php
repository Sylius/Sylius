<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\User\Repository\UserRepositoryInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class UserRepository extends EntityRepository implements UserRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function countBetweenDates(\DateTime $from, \DateTime $to, $status = null)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $queryBuilder
            ->andWhere($queryBuilder->expr()->gte('o.createdAt', ':from'))
            ->andWhere($queryBuilder->expr()->lte('o.createdAt', ':to'))
            ->setParameter('from', $from)
            ->setParameter('to', $to)
        ;

        if (null !== $status) {
            $queryBuilder
                ->andWhere('o.status = :status')
                ->setParameter('status', $status)
            ;
        }

        return $queryBuilder
            ->select('count(o.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getRegistrationStatistic(array $configuration = [])
    {
        $groupBy = '';
        foreach ($configuration['groupBy'] as $groupByArray) {
            $groupBy = $groupByArray.'(date)'.' '.$groupBy;
        }
        $groupBy = substr($groupBy, 0, -1);
        $groupBy = str_replace(' ', ', ', $groupBy);

        $queryBuilder = $this->getEntityManager()->getConnection()->createQueryBuilder();
        $tableName = $this->getEntityManager()->getClassMetadata($this->_entityName)->getTableName();

        $queryBuilder
            ->select('DATE(u.created_at) as date', ' count(u.id) as user_total')
            ->from($tableName, 'u')
            ->where($queryBuilder->expr()->gte('u.created_at', ':from'))
            ->andWhere($queryBuilder->expr()->lte('u.created_at', ':to'))
            ->setParameter('from', $configuration['start']->format('Y-m-d H:i:s'))
            ->setParameter('to', $configuration['end']->format('Y-m-d H:i:s'))
            ->groupBy($groupBy)
            ->orderBy($groupBy)
        ;

        return $queryBuilder
            ->execute()
            ->fetchAll()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByEmail($email)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $queryBuilder
            ->andWhere($queryBuilder->expr()->eq('o.emailCanonical', ':email'))
            ->setParameter('email', $email)
        ;

        return $queryBuilder
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
