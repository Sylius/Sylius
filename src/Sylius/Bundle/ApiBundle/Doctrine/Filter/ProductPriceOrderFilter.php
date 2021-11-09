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

namespace Sylius\Bundle\ApiBundle\Doctrine\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Common\Filter\OrderFilterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Core\Model\ProductInterface;

/** @experimental */
final class ProductPriceOrderFilter extends AbstractContextAwareFilter
{
    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    ): void {
        if ('order' !== $property || !isset($value['price'])) {
            return;
        }

        $entityRepository = $queryBuilder->getEntityManager()->getRepository(ProductInterface::class);

        $subQuery = $entityRepository->createQueryBuilder('m')
            ->select('min(v.position)')
            ->innerJoin('m.variants', 'v')
            ->andWhere('m.id = :product_id')
            ->andWhere('v.enabled = :enabled')
            ->setParameter('enabled', true)
        ;

        $rootAlias = $queryBuilder->getRootAliases()[0];

        $queryBuilder
            ->addSelect('variant')
            ->addSelect('channelPricing')
            ->innerJoin(sprintf('%s.variants', $rootAlias), 'variant')
            ->innerJoin('variant.channelPricings', 'channelPricing')
            ->andWhere(
                $queryBuilder->expr()->in(
                    'variant.position',
                    str_replace(':product_id', sprintf('%s.id', $rootAlias), $subQuery->getDQL())
                )
            )
            ->orderBy('channelPricing.price', $value['price'])
        ;
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'order[price]' => [
                'type' => 'string',
                'required' => false,
                'property' => 'price',
                'schema' => [
                    'type' => 'string',
                    'enum' => [
                        strtolower(OrderFilterInterface::DIRECTION_ASC),
                        strtolower(OrderFilterInterface::DIRECTION_DESC),
                    ],
                ],
            ]
        ];
    }
}
