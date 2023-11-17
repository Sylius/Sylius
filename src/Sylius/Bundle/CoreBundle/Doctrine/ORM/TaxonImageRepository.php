<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\TaxonImageInterface;
use Sylius\Component\Core\Repository\TaxonImageRepositoryInterface;

/**
 * @template T of TaxonImageInterface
 *
 * @implements TaxonImageRepositoryInterface<T>
 */
final class TaxonImageRepository extends EntityRepository implements TaxonImageRepositoryInterface
{
    public function findOneByIdAndOwnerCode(string $id, string $ownerCode): ?TaxonImageInterface
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.owner', 'owner')
            ->andWhere('o.id = :id')
            ->andWhere('owner.code = :ownerCode')
            ->setParameter('id', $id)
            ->setParameter('ownerCode', $ownerCode)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
