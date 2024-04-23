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

use ApiPlatform\Core\Bridge\Doctrine\Common\Filter\OrderFilterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;

final class TranslationOrderNameAndLocaleFilter extends AbstractContextAwareFilter
{
    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?string $operationName = null,
    ): void {
        if ('order' === $property) {
            if (!isset($value['translation.name'])) {
                return;
            }

            $direction = $value['translation.name'];
            $rootAlias = $queryBuilder->getRootAliases()[0];

            if (isset($value['localeCode'])) {
                $localeParameterName = $queryNameGenerator->generateParameterName('locale');

                $queryBuilder
                    ->addSelect('translation')
                    ->leftJoin(
                        sprintf('%s.translations', $rootAlias),
                        'translation',
                        'WITH',
                        sprintf('translation.locale = :%s', $localeParameterName),
                    )
                    ->orderBy('translation.name', $direction)
                    ->setParameter($localeParameterName, $value['localeCode'])
                ;

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
                    'enum' => [
                        strtolower(OrderFilterInterface::DIRECTION_ASC),
                        strtolower(OrderFilterInterface::DIRECTION_DESC),
                    ],
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
