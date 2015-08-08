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

use Pagerfanta\Pagerfanta;
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
     * @return Pagerfanta
     */
    public function createFilterPaginator($criteria = array(), $sorting = array(), $deleted = false)
    {
        $queryBuilder = parent::getCollectionQueryBuilder()
            ->leftJoin($this->getPropertyName('user'), 'user');

        if ($deleted) {
            $this->_em->getFilters()->disable('softdeleteable');
        }

        if (isset($criteria['query'])) {
            // Every tokenized search term should appear in at least one of the searchable fields,
            // for the entity to be included in the filtered results.
            $queryParts = preg_split('/\s+/', $criteria['query']);
            for($i=0; $i<count($queryParts); $i++) {
                $queryBuilder->andWhere(
                    $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->like($this->getPropertyName('emailCanonical'), ":query$i"),
                        $queryBuilder->expr()->like($this->getPropertyName('firstName'), ":query$i"),
                        $queryBuilder->expr()->like($this->getPropertyName('lastName'), ":query$i"),
                        $queryBuilder->expr()->like('user.username', ":query$i")
                    )
                )
                ->setParameter("query$i", '%'.$queryParts[$i].'%');
            }
        }

        if (isset($criteria['enabled'])) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->eq('user.enabled', ':enabled'))
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
