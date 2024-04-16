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

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;

final class ExchangeRateFilter extends AbstractContextAwareFilter
{
    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?string $operationName = null,
    ) {
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
