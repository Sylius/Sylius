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

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

/** @experimental */
final class ProductVariantOptionValueFilter extends AbstractContextAwareFilter
{
    private IriConverterInterface $iriConverter;

    public function __construct(
        IriConverterInterface $iriConverter,
        ManagerRegistry $managerRegistry,
        ?RequestStack $requestStack = null,
        LoggerInterface $logger = null,
        array $properties = null,
        NameConverterInterface $nameConverter = null
    ) {
        parent::__construct($managerRegistry, $requestStack, $logger, $properties, $nameConverter);

        $this->iriConverter = $iriConverter;
    }

    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    ): void {
        if ($property !== 'optionValues') {
            return;
        }

        $value = (array) $value;

        foreach ($value as $optionValueIri) {
            $optionValue = $this->iriConverter->getItemFromIri($optionValueIri);

            $parameterName = $queryNameGenerator->generateParameterName($property);
            $queryBuilder
                ->andWhere(sprintf(':%s MEMBER OF o.optionValues', $parameterName))
                ->setParameter($parameterName, $optionValue)
            ;
        }
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'optionValues' => [
                'type' => 'string',
                'required' => false,
                'property' => 'optionValues',
                'schema' => [
                    'type' => 'string',
                ],
            ],
            'optionValues[]' => [
                'type' => 'string',
                'required' => false,
                'property' => 'optionValues',
                'is_collection' => true,
            ],
        ];
    }
}
