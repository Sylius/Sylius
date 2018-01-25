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

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;

class AddressRepository extends EntityRepository implements AddressRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByCustomer(CustomerInterface $customer): array
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.customer', 'customer')
            ->andWhere('customer = :customer')
            ->setParameter('customer', $customer)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByCustomer(string $id, CustomerInterface $customer): ?AddressInterface
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.customer', 'customer')
            ->andWhere('o.id = :id')
            ->andWhere('customer = :customer')
            ->setParameter('id', $id)
            ->setParameter('customer', $customer)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
