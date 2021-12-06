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

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;

/** @experimental */
final class ExchangeRateFilter extends AbstractContextAwareFilter
{
    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    ) {
        if ($property === 'currencyCode') {
            $queryBuilder
                ->innerJoin('o.sourceCurrency', 'sourceCurrency')
                ->innerJoin('o.targetCurrency', 'targetCurrency')
                ->where('sourceCurrency.code LIKE CONCAT(\'%\', :code, \'%\')')
                ->orWhere('targetCurrency.code LIKE CONCAT(\'%\', :code, \'%\')')
                ->setParameter('code', $value)
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
