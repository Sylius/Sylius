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

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Sylius\Bundle\PromotionBundle\Doctrine\ORM\PromotionRepository as BasePromotionRepository;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Repository\PromotionRepositoryInterface;
use SyliusLabs\AssociationHydrator\AssociationHydrator;

/**
 * @template T of PromotionInterface
 *
 * @extends BasePromotionRepository<T>
 *
 * @implements PromotionRepositoryInterface<T>
 */
class PromotionRepository extends BasePromotionRepository implements PromotionRepositoryInterface
{
    private AssociationHydrator $associationHydrator;

    public function __construct(EntityManager $entityManager, ClassMetadata $class)
    {
        parent::__construct($entityManager, $class);

        $this->associationHydrator = new AssociationHydrator($entityManager, $class);
    }

    public function findActiveByChannel(ChannelInterface $channel): array
    {
        $promotions = $this->filterByActive($this->createQueryBuilder('o'))
            ->andWhere(':channel MEMBER OF o.channels')
            ->setParameter('channel', $channel)
            ->addOrderBy('o.priority', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        $this->associationHydrator->hydrateAssociations($promotions, [
            'rules',
        ]);

        return $promotions;
    }

    public function findActiveNonCouponBasedByChannel(ChannelInterface $channel): array
    {
        $promotions = $this->filterByActive($this->createQueryBuilder('o'))
            ->andWhere(':channel MEMBER OF o.channels')
            ->andWhere('o.couponBased = false')
            ->setParameter('channel', $channel)
            ->addOrderBy('o.priority', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        $this->associationHydrator->hydrateAssociations($promotions, [
            'rules',
        ]);

        return $promotions;
    }
}
