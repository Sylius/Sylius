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
    public function findOneByOrderId($id, $orderId)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        return $queryBuilder
            ->andWhere('o.id = :id')
            ->andWhere('o.order = :orderId')
            ->setParameter('id', $id)
            ->setParameter('orderId', $orderId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findByName($name, $locale)
    {
        return $this->createQueryBuilder('o')
            ->leftJoin('o.translations', 'translation')
            ->andWhere('translation.name = :name')
            ->andWhere('translation.locale = :locale')
            ->setParameter('name', $name)
            ->setParameter('localeCode', $locale)
            ->getQuery()
            ->getResult()
        ;
    }
}
