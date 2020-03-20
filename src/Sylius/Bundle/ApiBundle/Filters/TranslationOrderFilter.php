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

namespace Sylius\Bundle\ApiBundle\Filters;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;

final class TranslationOrderFilter extends AbstractContextAwareFilter
{
    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    ): void {
        if ($property === 'order') {
            foreach ($value as $field => $direction) {
                if (substr($field, 0, 11) !== 'translation') {
                    return;
                }

                $queryBuilder
                    ->addSelect('translation')
                    ->innerJoin('o.translations', 'translation')
                    ->orderBy($field, $direction)
                ;
            }
        }
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'translation' => [
                'type' => 'string',
                'required' => false,
                'property' => 'translation',
            ],
        ];
    }
}
