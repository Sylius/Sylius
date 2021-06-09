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

namespace Sylius\Bundle\ApiBundle\Doctrine\Filters;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;

/** @experimental */
final class TaxRateZoneFilter extends AbstractContextAwareFilter
{
    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    ) {
        if ($property === 'zoneCode') {
            $queryBuilder
                ->innerJoin('o.zone', 'zoneId')
                ->where('zoneId.code LIKE :code')
                ->setParameter('code', strrpos($value, '/') ? substr(strrchr($value, "/"), 1) : $value)
            ;
        }
    }

    public function getDescription(string $resourceClass): array
    {
        $description = [];

        $description['zoneCode'] = [
            'type' => 'string',
            'required' => false,
            'property' => 'zoneCode',
        ];

        return $description;
    }
}
