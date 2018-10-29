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

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping;
use Sylius\Bundle\PromotionBundle\Doctrine\ORM\PromotionRepository as BasePromotionRepository;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Repository\PromotionRepositoryInterface;
use SyliusLabs\AssociationHydrator\AssociationHydrator;

class PromotionRepository extends BasePromotionRepository implements PromotionRepositoryInterface
{
    /**
     * @var AssociationHydrator
     */
    private $associationHydrator;

    /**
     * {@inheritdoc}
     */
    public function __construct(EntityManager $entityManager, Mapping\ClassMetadata $class)
    {
        parent::__construct($entityManager, $class);

        $this->associationHydrator = new AssociationHydrator($entityManager, $class);
    }

    /**
     * {@inheritdoc}
     */
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
}
