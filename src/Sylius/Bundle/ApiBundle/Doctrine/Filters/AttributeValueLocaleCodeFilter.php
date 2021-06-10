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
final class AttributeValueLocaleCodeFilter extends AbstractContextAwareFilter
{
    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    ) {
        if ($property === 'localeCode') {
            $queryBuilder
                ->andWhere('o.localeCode LIKE :code OR o.localeCode IS NULL')
                ->setParameter('code', $value)
            ;
        }
    }

    public function getDescription(string $resourceClass): array
    {
        $description = [];

        $description['localeCode'] = [
            'type' => 'string',
            'required' => false,
            'property' => 'localeCode',
        ];

        return $description;
    }
}
