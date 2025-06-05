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
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

final class OwnerBasedProductAssociationTypesFilter extends AbstractFilter
{
    public function __construct(
        private readonly SectionProviderInterface $sectionProvider,
        private readonly string $productAssociationClass,
        ManagerRegistry $managerRegistry,
        ?LoggerInterface $logger = null,
        ?array $properties = null,
        ?NameConverterInterface $nameConverter = null,
    ) {
        parent::__construct($managerRegistry, $logger, $properties, $nameConverter);
    }

    protected function filterProperty(
        string $property,
        mixed $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        if (!is_a($resourceClass, ProductAssociationTypeInterface::class, true)) {
            return;
        }

        if (!$this->sectionProvider->getSection() instanceof ShopApiSection) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        $associationAlias = $queryNameGenerator->generateJoinAlias('productAssociations');
        $associatedProductAlias = $queryNameGenerator->generateJoinAlias('associatedProduct');

        $enabledParameter = $queryNameGenerator->generateParameterName('enabled');

        $queryBuilder
            ->innerJoin(
                $this->productAssociationClass,
                $associationAlias,
                Join::WITH,
                sprintf('%s.type = %s', $associationAlias, $rootAlias),
            )
            ->innerJoin(
                sprintf('%s.associatedProducts', $associationAlias),
                $associatedProductAlias,
                Join::WITH,
                sprintf('%s.enabled = :%s', $associatedProductAlias, $enabledParameter),
            )
            ->setParameter($enabledParameter, true)
        ;

        if ('productCode' !== $property || null === $value) {
            return;
        }

        $productAlias = $queryNameGenerator->generateJoinAlias('owner');
        $productCodeParameter = $queryNameGenerator->generateParameterName('productCode');

        $queryBuilder
            ->innerJoin(
                sprintf('%s.owner', $associationAlias),
                $productAlias,
                Join::WITH,
                sprintf('%s.code = :%s', $productAlias, $productCodeParameter),
            )
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
                    'description' => 'Filters association types by owner product code.',
                ],
            ],
        ];
    }
}
