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

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;

/**
 * Default channel repository.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ChannelRepository extends EntityRepository implements ChannelRepositoryInterface
{
    public function findMatchingHostname($hostname)
    {
        $queryBuilder = $this->createQueryBuilder('channel');

        $queryBuilder
            ->andWhere('channel.url LIKE :hostname')
            ->setParameter('hostname', '%'.$hostname.'%')
        ;

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}
