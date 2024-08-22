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

namespace Sylius\Bundle\ApiBundle\Doctrine\QueryCollectionExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\ContextAwareQueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface as LegacyQueryNameGeneratorInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Resource\Model\TranslatableInterface;

final class TranslationOrderLocaleExtension implements ContextAwareQueryCollectionExtensionInterface
{
    /** @param array<string, mixed> $context */
    public function applyToCollection(
        QueryBuilder $queryBuilder,
        LegacyQueryNameGeneratorInterface|QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?string $operationName = null,
        array $context = [],
    ): void {
        if (!is_a($resourceClass, TranslatableInterface::class, true)) {
            return;
        }
        /* @see \Sylius\Bundle\ApiBundle\Filter\Doctrine\TranslationOrderNameAndLocaleFilter */
        if (!isset($context['filters']['order']['translation.name'])) {
            return;
        }
        if (!$queryBuilder->getEntityManager()->getClassMetadata($resourceClass)->hasAssociation('translations')) {
            return;
        }

        $localeCode = $this->resolveContextLocaleCode($context);
        if (empty($localeCode)) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $localeCodeParameterName = $queryNameGenerator->generateParameterName('localeCode');

        $queryBuilder
            ->addSelect('translation')
            ->leftJoin(
                sprintf('%s.translations', $rootAlias),
                'translation',
                Join::WITH,
                sprintf('translation.locale = :%s', $localeCodeParameterName),
            )
            ->setParameter($localeCodeParameterName, $localeCode)
        ;
    }

    /** @param array<string, mixed> $context */
    private function resolveContextLocaleCode(array $context): ?string
    {
        return
            $context['filters']['localeCode for order']['translation.name'] ??
            $context['filters']['order']['localeCode']['translation.name'] ??
            $context['filters']['order']['localeCode'] ??
            null
        ;
    }
}
