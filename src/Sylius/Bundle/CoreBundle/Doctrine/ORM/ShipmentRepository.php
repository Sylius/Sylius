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
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;

class ShipmentRepository extends EntityRepository implements ShipmentRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findByOrderIdAndId($orderId, $id)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        return $queryBuilder
            ->where('o.order = :orderId')
            ->andWhere('o.id = :id')
            ->setParameter('orderId', $orderId)
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findByName($name, $localeCode)
    {
        return $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->leftJoin('o.translations', 'translation')
            ->where('translation.name = :name')
            ->andWhere('translation.locale = :localeCode')
            ->setParameter('name', $name)
            ->setParameter('localeCode', $localeCode)
            ->getQuery()
            ->getResult()
        ;
    }
}
