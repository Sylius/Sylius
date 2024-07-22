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

namespace Sylius\Bundle\ApiBundle\Doctrine\ORM\Filters;

use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

final class PromotionCouponPromotionFilter extends AbstractFilter
{
    public const PROPERTY = 'promotion';

    /** @param array<array-key, mixed> $properties */
    public function __construct(
        private IriConverterInterface $iriConverter,
        ManagerRegistry $managerRegistry,
        ?RequestStack $requestStack = null,
        ?LoggerInterface $logger = null,
        ?array $properties = null,
        ?NameConverterInterface $nameConverter = null,
    ) {
        parent::__construct($managerRegistry, $requestStack, $logger, $properties, $nameConverter);
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
        if (self::PROPERTY !== $property) {
            return;
        }

        $promotion = $this->iriConverter->getResourceFromIri($value);

        $parameterName = $queryNameGenerator->generateParameterName(':promotion');
        $promotionJoinAlias = $queryNameGenerator->generateJoinAlias('promotion');
        $rootAlias = $queryBuilder->getRootAliases()[0];

        $queryBuilder
            ->innerJoin(
                sprintf('%s.promotion', $rootAlias),
                $promotionJoinAlias,
                Join::WITH,
                $queryBuilder->expr()->eq(sprintf('%s.id', $promotionJoinAlias), $parameterName),
            )
            ->setParameter($parameterName, $promotion)
        ;
    }

    /** @return array<string, mixed> */
    public function getDescription(string $resourceClass): array
    {
        return [
            self::PROPERTY => [
                'type' => 'string',
                'required' => false,
                'property' => null,
                'description' => 'Get a collection of promotion coupons for promotion',
                'schema' => [
                    'type' => 'string',
                ],
            ],
        ];
    }
}
