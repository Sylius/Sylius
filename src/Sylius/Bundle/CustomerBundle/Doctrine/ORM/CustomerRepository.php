<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CustomerBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Customer\Repository\CustomerRepositoryInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class CustomerRepository extends EntityRepository implements CustomerRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return (int) $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findLatest($count)
    {
        return $this->createQueryBuilder('o')
            ->addOrderBy('o.createdAt', 'desc')
            ->setMaxResults($count)
            ->getQuery()
            ->getResult()
        ;
    }
}
