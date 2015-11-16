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

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\PagerfantaInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\ResourceRepository;
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class UserRepository extends ResourceRepository implements UserRepositoryInterface
{
    /**
     * @param array $criteria
     * @param array $sorting
     * @param bool  $deleted
     *
     * @return PagerfantaInterface
     */
    public function createFilterPaginator($criteria = array(), $sorting = array(), $deleted = false)
    {
        $queryBuilder = $this->objectRepository->createQueryBuilder('o');

        if ($deleted) {
            $this->objectManager->getFilters()->disable('softdeleteable');
        }

        if (isset($criteria['query'])) {
            $queryBuilder
                ->leftJoin('o.customer', 'customer')
                ->where('customer.emailCanonical LIKE :query')
                ->orWhere('customer.firstName LIKE :query')
                ->orWhere('customer.lastName LIKE :query')
                ->orWhere('o.username LIKE :query')
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
        $this->objectManager->getFilters()->disable('softdeleteable');

        $queryBuilder = $this->objectRepository->createQueryBuilder('o');
        $queryBuilder
            ->leftJoin('o.customer', 'customer')
            ->addSelect('customer')
            ->where($queryBuilder->expr()->eq('o.id', ':id'))
            ->setParameter('id', $id)
        ;

        $result = $queryBuilder
            ->getQuery()
            ->getOneOrNullResult()
        ;
        $this->objectManager->getFilters()->enable('softdeleteable');

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

    /**
     * @param array $configuration
     *
     * @return array
     */
    public function getRegistrationStatistic(array $configuration = array())
    {
        $groupBy = '';
        foreach ($configuration['groupBy'] as $groupByArray) {
            $groupBy = $groupByArray.'(date)'.' '.$groupBy;
        }
        $groupBy = substr($groupBy, 0, -1);
        $groupBy = str_replace(' ', ', ', $groupBy);

        $queryBuilder = $this->objectManager->getConnection()->createQueryBuilder();

        $queryBuilder
            ->select('DATE(u.created_at) as date', ' count(u.id) as user_total')
            ->from('sylius_user', 'u')
            ->where($queryBuilder->expr()->gte('u.created_at', ':from'))
            ->andWhere($queryBuilder->expr()->lte('u.created_at', ':to'))
            ->setParameter('from', $configuration['start']->format('Y-m-d H:i:s'))
            ->setParameter('to', $configuration['end']->format('Y-m-d H:i:s'))
            ->groupBy($groupBy)
            ->orderBy($groupBy)
        ;

        return $queryBuilder
            ->execute()
            ->fetchAll();
    }

    /**
     * @param string $email
     *
     * @return mixed
     *
     * @throws NonUniqueResultException
     */
    public function findOneByEmail($email)
    {
        $queryBuilder = $this->objectRepository->createQueryBuilder('o');

        $queryBuilder
            ->leftJoin('o.customer', 'customer')
            ->andWhere($queryBuilder->expr()->eq('customer.emailCanonical', ':email'))
            ->setParameter('email', $email)
        ;

        return $queryBuilder
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     *
     * @return QueryBuilder
     */
    protected function getCollectionQueryBuilderBetweenDates(\DateTime $from, \DateTime $to)
    {
        $queryBuilder = $this->objectRepository->createQueryBuilder('o');

        return $queryBuilder
            ->andWhere($queryBuilder->expr()->gte('o.createdAt', ':from'))
            ->andWhere($queryBuilder->expr()->lte('o.createdAt', ':to'))
            ->setParameter('from', $from)
            ->setParameter('to', $to)
        ;
    }
}
