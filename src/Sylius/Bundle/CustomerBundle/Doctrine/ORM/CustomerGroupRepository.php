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

namespace Sylius\Bundle\CustomerBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Sylius\Component\Customer\Repository\CustomerGroupRepositoryInterface;

/**
 * @template T of CustomerGroupInterface
 *
 * @implements CustomerGroupRepositoryInterface<T>
 */
class CustomerGroupRepository extends EntityRepository implements CustomerGroupRepositoryInterface
{
    public function findByPhrase(string $phrase, ?int $limit = null): iterable
    {
        $expr = $this->getEntityManager()->getExpressionBuilder();

        return $this->createQueryBuilder('o')
            ->andWhere($expr->orX(
                'o.code LIKE :phrase',
                'o.name LIKE :phrase',
            ))
            ->setParameter('phrase', '%' . $phrase . '%')
            ->addOrderBy('o.name', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }
}
