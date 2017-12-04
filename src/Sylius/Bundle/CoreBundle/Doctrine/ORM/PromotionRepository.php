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

use Sylius\Bundle\PromotionBundle\Doctrine\ORM\PromotionRepository as BasePromotionRepository;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Repository\PromotionRepositoryInterface;

class PromotionRepository extends BasePromotionRepository implements PromotionRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findActiveByChannel(ChannelInterface $channel): array
    {
        return $this->filterByActive($this->createQueryBuilder('o'))
            ->andWhere(':channel MEMBER OF o.channels')
            ->setParameter('channel', $channel)
            ->addOrderBy('o.priority', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
}
