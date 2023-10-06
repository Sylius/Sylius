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

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;

/** @experimental */
final class ExchangeRateFilter extends AbstractFilter
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
        if ($property === 'currencyCode') {
            $rootAlias = $queryBuilder->getRootAliases()[0];
            $codeParameterName = $queryNameGenerator->generateParameterName('code');
            $queryBuilder
                ->innerJoin(sprintf('%s.sourceCurrency', $rootAlias), 'sourceCurrency')
                ->innerJoin(sprintf('%s.targetCurrency', $rootAlias), 'targetCurrency')
                ->where('sourceCurrency.code LIKE CONCAT(\'%\', :' . $codeParameterName . ', \'%\')')
                ->orWhere('targetCurrency.code LIKE CONCAT(\'%\', :' . $codeParameterName . ', \'%\')')
                ->setParameter($codeParameterName, $value)
            ;
        }
    }

    public function getDescription(string $resourceClass): array
    {
        $description = [];

        $description['currencyCode'] = [
            'type' => 'string',
            'required' => false,
            'property' => 'currencyCode',
        ];

        return $description;
    }
}
