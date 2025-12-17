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

namespace Sylius\Bundle\ApiBundle\Doctrine\ORM\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

final class ProductBasedProductOptionFilter extends AbstractFilter
{
    public function __construct(
        private string $productClass,
        ManagerRegistry $managerRegistry,
        ?LoggerInterface $logger = null,
        ?array $properties = null,
        ?NameConverterInterface $nameConverter = null,
    ) {
        parent::__construct($managerRegistry, $logger, $properties, $nameConverter);
    }

    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        if (!is_a($resourceClass, ProductOptionInterface::class, true)) {
            return;
        }
        if ('productCode' !== $property || $value === null || $value === '') {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        $productJoinAlias = $queryNameGenerator->generateJoinAlias('product');

        $productCodeParameter = $queryNameGenerator->generateParameterName('productCode');

        $queryBuilder
            ->innerJoin(
                $this->productClass,
                $productJoinAlias,
                Join::WITH,
                sprintf('%s.code = :%s', $productJoinAlias, $productCodeParameter),
            )
            ->andWhere(sprintf('%s MEMBER OF %s.options', $rootAlias, $productJoinAlias))
            ->setParameter($productCodeParameter, $value)
        ;
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'productCode' => [
                'property' => 'productCode',
                'type' => 'string',
                'required' => false,
                'swagger' => [
                    'description' => 'Filter by product code',
                ],
            ],
        ];
    }
}
