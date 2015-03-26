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

use Doctrine\ORM\QueryBuilder;
use Pagerfanta\PagerfantaInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\CustomerInterface;

class CustomerRepository extends EntityRepository
{
    /**
     * Create filter paginator.
     *
     * @param array $criteria
     * @param array $sorting
     * @param bool  $registered
     *
     * @return PagerfantaInterface
     */
    public function createFilterPaginator($criteria = array(), $sorting = array(), $registered = false)
    {
        if ($registered) {
            $qb = parent::getCollectionQueryBuilder();
            $qb->addSelect('user');
            $qb->innerJoin('o.user', 'user');
        } else {
            $qb = $this->getCollectionQueryBuilder();
        }

        if (isset($criteria['query'])) {
            $qb
                ->andWhere('o.email LIKE :query')
                ->orWhere('o.firstName LIKE :query')
                ->orWhere('o.lastName LIKE :query')
                ->setParameter('query', '%'.$criteria['query'].'%')
            ;
        }

        if (empty($sorting)) {
            if (!is_array($sorting)) {
                $sorting = array();
            }
            $sorting['id'] = 'asc';
        }

        $this->applySorting($qb, $sorting);

        return $this->getPaginator($qb);
    }

    /**
     * Get the customer data for the details page.
     *
     * @param int $id
     *
     * @return null|CustomerInterface
     */
    public function findForDetailsPage($id)
    {
        $qb = $this->getQueryBuilder();
        $qb
            ->andWhere($qb->expr()->eq('o.id', ':id'))
            ->setParameter('id', $id)
        ;

        return $qb
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     *
     * @return mixed
     */
    public function countBetweenDates(\DateTime $from, \DateTime $to)
    {
        $qb = $this->getCollectionQueryBuilderBetweenDates($from, $to);

        return $qb
            ->select('count(o.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    protected function getCollectionQueryBuilderBetweenDates(\DateTime $from, \DateTime $to)
    {
        $qb = $this->getCollectionQueryBuilder();

        return $qb
            ->andWhere($qb->expr()->gte('o.createdAt', ':from'))
            ->andWhere($qb->expr()->lte('o.createdAt', ':to'))
            ->setParameter('from', $from)
            ->setParameter('to', $to)
        ;
    }

    /**
     * @return QueryBuilder
     */
    protected function getCollectionQueryBuilder()
    {
        return $this->getQueryBuilder();
    }

    /**
     * @return QueryBuilder
     */
    protected function getQueryBuilder()
    {
        $qb = parent::getQueryBuilder();
        $qb->addSelect('user');
        $qb->leftJoin('o.user', 'user');

        return $qb;
    }
}
