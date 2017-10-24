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

use Sylius\Bundle\UserBundle\Doctrine\ORM\UserRepository as BaseUserRepository;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;

class UserRepository extends BaseUserRepository implements UserRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findOneByEmail(string $email): ?UserInterface
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.customer', 'customer')
            ->andWhere('customer.emailCanonical = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
