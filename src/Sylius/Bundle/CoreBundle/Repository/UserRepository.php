<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Repository;

use Sylius\Bundle\CoreBundle\Model\UserInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

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
     * @param array   $criteria
     * @param array   $sorting
     * @param boolean $deleted
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
                ->where($this->getAlias().'.username LIKE :query')
                ->orWhere($this->getAlias().'.email LIKE :query')
                ->orWhere($this->getAlias().'.firstName LIKE :query')
                ->orWhere($this->getAlias().'.lastName LIKE :query')
                ->setParameter('query', '%'.$criteria['query'].'%')
            ;
        }
        if (isset($criteria['enabled'])) {
            $queryBuilder
                ->andWhere($this->getAlias().'.enabled = :enabled')
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
        $queryBuilder = $this->getQueryBuilder();

        $this->_em->getFilters()->disable('softdeleteable');

        $queryBuilder
            ->andWhere($queryBuilder->expr()->eq($this->getAlias().'.id', ':id'))
            ->setParameter('id', $id)
        ;

        $result = $queryBuilder
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $result;
    }

    /**
     * @param \DateTime    $from
     * @param \DateTime    $to
     * @param null|integer $status
     *
     * @return integer
     */
    public function countBetweenDates(\DateTime $from, \DateTime $to, $status = null)
    {
        $queryBuilder = $this->getCollectionQueryBuilderBetweenDates($from, $to);
        if (null !== $status) {
            $queryBuilder
                ->andWhere($this->getAlias().'.status = :status')
                ->setParameter('status', $status)
            ;
        }

        return $queryBuilder
            ->select('count(o.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    protected function getCollectionQueryBuilderBetweenDates(\DateTime $from, \DateTime $to)
    {
        $queryBuilder = $this->getCollectionQueryBuilder();

        return $queryBuilder
            ->andWhere($queryBuilder->expr()->gte($this->getAlias().'.createdAt', ':from'))
            ->andWhere($queryBuilder->expr()->lte($this->getAlias().'.createdAt', ':to'))
            ->setParameter('from', $from)
            ->setParameter('to', $to)
        ;
    }

    protected function getAlias()
    {
        return 'u';
    }
}
