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
use Sylius\Component\Core\Model\ProductInterface;

/** @experimental */
final class TranslationOrderNameAndLocaleFilter extends AbstractContextAwareFilter
{
    private const ALLOWED_DIRECTIONS = ['asc', 'desc'];

    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    ): void {
        if ('order' === $property) {
            if (!isset($value['translation.name'])) {
                return;
            }

            $direction = strtolower($value['translation.name']);
            if (!in_array($direction, self::ALLOWED_DIRECTIONS, true)) {
                return;
            }

            if (is_a($resourceClass, ProductInterface::class, true)) {
                $queryBuilder->orderBy('translation.name', $direction);

                return;
            }

            if (isset($value['localeCode'])) {
                $queryBuilder
                    ->addSelect('translation')
                    ->innerJoin(
                        sprintf('%s.translations', $queryBuilder->getRootAliases()[0]),
                        'translation',
                        'WITH',
                        'translation.locale = :locale'
                    )
                    ->orderBy('translation.name', $direction)
                    ->setParameter('locale', $value['localeCode'])
                ;

                return;
            }

            $queryBuilder
                ->addSelect('translation')
                ->innerJoin('o.translations', 'translation')
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
            'localeCode for order[translation.name]' => [
                'type' => 'string',
                'required' => false,
                'property' => 'localeCode',
            ],
        ];
    }
}
