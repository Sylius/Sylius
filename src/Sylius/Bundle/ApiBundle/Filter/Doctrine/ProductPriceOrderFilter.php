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

namespace Sylius\Bundle\ApiBundle\Filter\Doctrine;

use ApiPlatform\Doctrine\Common\Filter\OrderFilterInterface;
use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\ProductInterface;
use Webmozart\Assert\Assert;

final class ProductPriceOrderFilter extends AbstractFilter
{
    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        Operation $operation = null,
        array $context = [],
    ): void {
        if ('order' !== $property || !isset($value['price'])) {
            return;
        }

        Assert::keyExists($context, ContextKeys::CHANNEL);
        $channel = $context[ContextKeys::CHANNEL];

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
                    str_replace(sprintf(':%s', $productIdParameterName), sprintf('%s.id', $rootAlias), $subQuery->getDQL()),
                ),
            )
            ->andWhere('channelPricing.channelCode = :channelCode')
            ->orderBy('channelPricing.price', $value['price'])
            ->setParameter($enabledParameterName, true)
            ->setParameter('channelCode', $channel->getCode())
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
