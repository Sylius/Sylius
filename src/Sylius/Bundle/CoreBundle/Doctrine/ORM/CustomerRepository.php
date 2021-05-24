<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM;

use Pagerfanta\Pagerfanta;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Search\Model\SearchQueryInterface;

class CustomerRepository extends EntityRepository implements CustomerRepositoryInterface
{
    public function countCustomers(): int
    {
        return (int) $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function countCustomersInPeriod(\DateTimeInterface $startDate, \DateTimeInterface $endDate): int
    {
        return (int) $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->where('o.createdAt >= :startDate')
            ->andWhere('o.createdAt <= :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function findLatest(int $count): array
    {
        return $this->createQueryBuilder('o')
            ->addOrderBy('o.createdAt', 'DESC')
            ->setMaxResults($count)
            ->getQuery()
            ->getResult()
        ;
    }

    public function searchWithoutTerms(): Pagerfanta
    {
        $queryBuilder = $this->createQueryBuilder('o');

        return $this->getPaginator($queryBuilder);
    }

    public function searchByTerms(SearchQueryInterface $query): Pagerfanta
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->where('MATCH_AGAINST(o.firstName, o.lastName, o.email, :terms) > 0.6')
            ->orWhere('CONCAT(o.firstName, o.lastName, o.email) LIKE :likeTerms')
            ->setParameters([
                'terms' => $query->getTerms(),
                'likeTerms' => sprintf('%%%s%%', str_replace(' ', '%', $query->getTerms())),
            ])
            ->orderBy('MATCH_AGAINST(o.firstName, o.lastName, o.email, :terms \'IN BOOLEAN MODE\')', 'DESC')
        ;

        return $this->getPaginator($queryBuilder);
    }
}
