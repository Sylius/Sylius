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

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class AddressRepository extends EntityRepository implements AddressRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByCustomer(CustomerInterface $customer)
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.customers','customer')
            ->where('customer.id = customer_id')
            ->setParameter('customer_id', $customer->getId())
            ->getQuery()
            ->getResult()
        ;
    }
}
