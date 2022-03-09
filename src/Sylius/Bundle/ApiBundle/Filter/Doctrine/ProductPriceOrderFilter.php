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

namespace Sylius\Bundle\ApiBundle\Filter\Doctrine;

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

        $productIdParameterName = $queryNameGenerator->generateParameterName('productId');
        $enabledParameterName = $queryNameGenerator->generateParameterName('enabled');

        $subQuery = $entityRepository->createQueryBuilder('m')
            ->select('min(v.position)')
            ->innerJoin('m.variants', 'v')
            ->andWhere(sprintf('m.id = :%s', $productIdParameterName))
            ->andWhere(sprintf('v.enabled = :%s', $enabledParameterName))
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
                    str_replace(sprintf(':%s', $productIdParameterName), sprintf('%s.id', $rootAlias), $subQuery->getDQL())
                )
            )
            ->orderBy('channelPricing.price', $value['price'])
            ->setParameter($enabledParameterName, true)
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
            ],
        ];
    }
}
