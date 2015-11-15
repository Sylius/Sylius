<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ChannelBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\ResourceRepository;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;

/**
 * Default channel repository.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ChannelRepository extends ResourceRepository implements ChannelRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function findMatchingHostname($hostname)
    {
        $queryBuilder = $this->objectRepository->createQueryBuilder('o');

        $queryBuilder
            ->andWhere($queryBuilder->expr()->like('o.url', ':hostname'))
            ->setParameter('hostname', '%'.$hostname.'%')
        ;

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * {@inheritDoc}
     */
    public function findDefault()
    {
        return $this->objectRepository->createQueryBuilder('o')
            ->getQuery()
            ->setMaxResults(1)
            ->getSingleResult()
        ;
    }
}
