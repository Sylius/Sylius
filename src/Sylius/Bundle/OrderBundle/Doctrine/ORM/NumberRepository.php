<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Bundle\OrderBundle\Repository\NumberRepositoryInterface;
use Doctrine\ORM\NoResultException;

class NumberRepository extends EntityRepository implements NumberRepositoryInterface
{
    public function getLastNumber()
    {
        try {
            return $this->getQueryBuilder()
                ->select($this->getAlias().'.id')
                ->orderBy($this->getAlias().'.id', 'desc')
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleScalarResult()
            ;
        } catch (NoResultException $e) {
            return null;
        }
    }
}
