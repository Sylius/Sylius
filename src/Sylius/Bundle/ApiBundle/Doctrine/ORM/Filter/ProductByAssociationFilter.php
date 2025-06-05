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
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

final class ProductByAssociationFilter extends AbstractFilter
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
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        if (!is_a($resourceClass, ProductInterface::class, true)) {
            return;
        }

        if (!$this->sectionProvider->getSection() instanceof ShopApiSection) {
            return;
        }

        if ($property !== 'association' || !is_array($value)) {
            return;
        }

        $associationTypeCode = $value['typeCode'] ?? '';
        $ownerCode = $value['ownerCode'] ?? '';

        if (
            (!is_string($associationTypeCode) && !is_string($ownerCode)) ||
            ($associationTypeCode === '' && $ownerCode === '')
        ) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $associationAlias = $queryNameGenerator->generateJoinAlias('association');

        $queryBuilder
            ->innerJoin(
                $this->productAssociationClass,
                $associationAlias,
                Join::WITH,
                sprintf('%s MEMBER OF %s.associatedProducts', $rootAlias, $associationAlias),
            )
        ;

        if (is_string($associationTypeCode) && $associationTypeCode !== '') {
            $associationTypeJoinAlias = $queryNameGenerator->generateJoinAlias('associationType');
            $typeCodeParameter = $queryNameGenerator->generateParameterName('typeCode');

            $queryBuilder
                ->leftJoin(sprintf('%s.type', $associationAlias), $associationTypeJoinAlias)
                ->andWhere(sprintf('%s.code = :%s', $associationTypeJoinAlias, $typeCodeParameter))
                ->setParameter($typeCodeParameter, $associationTypeCode)
            ;
        }

        if (is_string($ownerCode) && $ownerCode !== '') {
            $associationOwnerAlias = $queryNameGenerator->generateJoinAlias('owner');
            $ownerCodeParameter = $queryNameGenerator->generateParameterName('ownerCode');

            $queryBuilder
                ->innerJoin(
                    sprintf('%s.owner', $associationAlias),
                    $associationOwnerAlias,
                    Join::WITH,
                    sprintf('%s.code = :%s', $associationOwnerAlias, $ownerCodeParameter),
                )
                ->setParameter($ownerCodeParameter, $ownerCode)
            ;
        }
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'association[typeCode]' => [
                'property' => 'association',
                'type' => 'string',
                'required' => false,
                'description' => 'Filter by association type code.',
            ],
            'association[ownerCode]' => [
                'property' => 'association',
                'type' => 'string',
                'required' => false,
                'description' => 'Filter by association owner code.',
            ],
        ];
    }
}
