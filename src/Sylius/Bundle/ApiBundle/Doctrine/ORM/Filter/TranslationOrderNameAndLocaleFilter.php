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
use Doctrine\ORM\QueryBuilder;

final class TranslationOrderNameAndLocaleFilter extends AbstractFilter
{
    private const ALLOWED_DIRECTIONS = ['asc', 'desc'];

    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        if ('order' === $property && isset($value['translation.name'])) {
            /** @phpstan-ignore-next-line */
            if (!$queryBuilder->getEntityManager()->getClassMetadata($resourceClass)->hasAssociation('translations')) {
                return;
            }

            $direction = strtolower($value['translation.name']);
            if (!in_array($direction, self::ALLOWED_DIRECTIONS, true)) {
                return;
            }

            $queryBuilder
                ->orderBy('translation.name', $direction)
            ;
        }
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'order[translation.name]' => [
                'type' => 'string',
                'required' => false,
                'property' => 'translation',
                'schema' => [
                    'type' => 'string',
                    'enum' => self::ALLOWED_DIRECTIONS,
                ],
            ],
            /* @see \Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Common\TranslationOrderLocaleExtension */
            'localeCode for order[translation.name]' => [
                'type' => 'string',
                'required' => false,
                'property' => 'localeCode',
            ],
        ];
    }
}
