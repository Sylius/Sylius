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

use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

final class ProductVariantCatalogPromotionFilter extends AbstractContextAwareFilter
{
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
        ?string $operationName = null,
    ): void {
        if ('catalogPromotion' !== $property) {
            return;
        }

        $catalogPromotion = $this->iriConverter->getResourceFromIri($value);

        $parameterName = $queryNameGenerator->generateParameterName($property);
        $channelPricingJoinAlias = $queryNameGenerator->generateJoinAlias('channelPricing');
        $appliedPromotionJoinAlias = $queryNameGenerator->generateJoinAlias('appliedPromotion');
        $rootAlias = $queryBuilder->getRootAliases()[0];

        $queryBuilder
            ->leftJoin(sprintf('%s.channelPricings', $rootAlias), $channelPricingJoinAlias)
            ->innerJoin(
                sprintf('%s.appliedPromotions', $channelPricingJoinAlias),
                $appliedPromotionJoinAlias,
                Join::WITH,
                sprintf('%s = :%s', $appliedPromotionJoinAlias, $parameterName),
            )
            ->setParameter($parameterName, $catalogPromotion)
        ;
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'catalogPromotion' => [
                'type' => 'string',
                'required' => false,
                'property' => null,
                'description' => 'Get a collection of product variants with applied catalog promotion',
                'schema' => [
                    'type' => 'string',
                ],
            ],
        ];
    }
}
