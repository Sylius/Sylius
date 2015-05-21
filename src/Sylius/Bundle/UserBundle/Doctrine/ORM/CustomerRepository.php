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
use Sylius\Component\Core\Model\UserInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class CustomerRepository extends EntityRepository
{
    /**
     * Get the customer's data for the details page.
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
                ->leftJoin($this->getAlias().'.user', 'user')
                ->where($queryBuilder->expr()->like($this->getAlias().'.emailCanonical', ':query'))
                ->orWhere($queryBuilder->expr()->like($this->getAlias().'.firstName', ':query'))
                ->orWhere($queryBuilder->expr()->like($this->getAlias().'.lastName', ':query'))
                ->orWhere($queryBuilder->expr()->like('user.username', ':query'))
                ->setParameter('query', '%'.$criteria['query'].'%')
            ;
        }
        if (isset($criteria['enabled'])) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->eq($this->getAlias().'.enabled', ':enabled'))
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
}
