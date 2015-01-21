<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM;

use Pagerfanta\PagerfantaInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\UserInterface;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * User repository.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class UserRepository extends EntityRepository
{
    /**
     * Create filter paginator.
     *
     * @param array $criteria
     * @param array $sorting
     * @param bool  $deleted
     *
     * @return PagerfantaInterface
     */
    public function createFilterPaginator($criteria = array(), $sorting = array(), $deleted = false)
    {
        $queryBuilder = parent::getCollectionQueryBuilder();

        if ($deleted) {
            $this->_em->getFilters()->disable('softdeleteable');
        }

        if (isset($criteria['query'])) {
            $queryBuilder
                ->where('o.username LIKE :query')
                ->orWhere('o.email LIKE :query')
                ->orWhere('o.firstName LIKE :query')
                ->orWhere('o.lastName LIKE :query')
                ->setParameter('query', '%'.$criteria['query'].'%')
            ;
        }
        if (isset($criteria['enabled'])) {
            $queryBuilder
                ->andWhere('o.enabled = :enabled')
                ->setParameter('enabled', $criteria['enabled'])
            ;
        }

        if (empty($sorting)) {
            if (!is_array($sorting)) {
                $sorting = array();
            }
            $sorting['updatedAt'] = 'desc';
        }

        $this->applySorting($queryBuilder, $sorting);

        return $this->getPaginator($queryBuilder);
    }

    /**
     * Get the user data for the details page.
     *
     * @param integer $id
     *
     * @return null|UserInterface
     */
    public function findForDetailsPage($id)
    {
        $this->_em->getFilters()->disable('softdeleteable');

        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder
            ->andWhere($queryBuilder->expr()->eq('o.id', ':id'))
            ->setParameter('id', $id)
        ;

        $result = $queryBuilder
            ->getQuery()
            ->getOneOrNullResult()
        ;
        $this->_em->getFilters()->enable('softdeleteable');

        return $result;
    }

    /**
     * @param \DateTime   $from
     * @param \DateTime   $to
     * @param null|string $status
     *
     * @return mixed
     */
    public function countBetweenDates(\DateTime $from, \DateTime $to, $status = null)
    {
        $queryBuilder = $this->getCollectionQueryBuilderBetweenDates($from, $to);
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

    public function getDailyStatistic(array $configuration=array())
    {
        $sql = '
        SELECT 
            date(user.created_at) as date, 
            count(user.id) as "user_total"
        FROM sylius_user user
        WHERE 
            user.created_at BETWEEN "'.$configuration['start']->format('Y-m-d H:i:s').'" AND "'.$configuration['end']->format('Y-m-d H:i:s').'"
        GROUP BY date(user.created_at)
        ORDER BY date(user.created_at)';

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getWeeklyStatistic(array $configuration=array())
    {
        $sql = '
        SELECT 
            CONCAT_WS(
                " ",
                "week",
                weekofyear(user.created_at),
                "of year",
                year(user.created_at)
                ) as date, 
            count(user.id) as "user_total"
        FROM sylius_user user
        WHERE 
            user.created_at BETWEEN "'.$configuration['start']->format('Y-m-d H:i:s').'" AND "'.$configuration['end']->format('Y-m-d H:i:s').'"
        GROUP BY year(user.created_at), weekofyear(user.created_at)
        ORDER BY year(user.created_at), weekofyear(user.created_at)';

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getMonthlyStatistic(array $configuration=array())
    {
        $sql = '
        SELECT 
            CONCAT_WS(
                " ",
                monthname(user.created_at),
                year(user.created_at)
                ) as date, 
            count(user.id) as "user_total"
        FROM sylius_user user
        WHERE 
            user.created_at BETWEEN "'.$configuration['start']->format('Y-m-d H:i:s').'" AND "'.$configuration['end']->format('Y-m-d H:i:s').'"
        GROUP BY year(user.created_at), monthname(user.created_at)
        ORDER BY year(user.created_at), month(user.created_at)';

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getYearlyStatistic(array $configuration=array())
    {
        $sql = '
        SELECT 
            year(user.created_at) as date, 
            count(user.id) as "user_total"
        FROM sylius_user user
        WHERE 
            year(user.created_at) BETWEEN year("'.$configuration['start']->format('Y-m-d H:i:s').'") AND ("'.$configuration['end']->format('Y-m-d H:i:s').'")
        GROUP BY year(user.created_at)
        ORDER BY year(user.created_at);';

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    protected function getCollectionQueryBuilderBetweenDates(\DateTime $from, \DateTime $to)
    {
        $queryBuilder = $this->getCollectionQueryBuilder();

        return $queryBuilder
            ->andWhere($queryBuilder->expr()->gte('o.createdAt', ':from'))
            ->andWhere($queryBuilder->expr()->lte('o.createdAt', ':to'))
            ->setParameter('from', $from)
            ->setParameter('to', $to)
        ;
    }
}
