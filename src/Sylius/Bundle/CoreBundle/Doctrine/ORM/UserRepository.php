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

use Sylius\Bundle\UserBundle\Doctrine\ORM\UserRepository as BaseUserRepository;
use Sylius\Component\User\Repository\UserRepositoryInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class UserRepository extends BaseUserRepository implements UserRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findOneByEmail($email)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        $queryBuilder
            ->innerJoin('o.customer', 'customer')
            ->andWhere($queryBuilder->expr()->eq('customer.emailCanonical', ':email'))
            ->setParameter('email', $email)
        ;

        return $queryBuilder
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
