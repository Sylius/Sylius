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

namespace Sylius\Bundle\AdminBundle\Doctrine\Query\Taxon;

use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;

final class AllTaxons implements AllTaxonsInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LocaleContextInterface $localeContext,
        private readonly TranslationLocaleProviderInterface $translationLocaleProvider,
    ) {
    }

    public function getArrayResult(): array
    {
        $fallbackLocale = $this->translationLocaleProvider->getDefaultLocaleCode();
        try {
            $currentLocale = $this->localeContext->getLocaleCode();
        } catch (LocaleNotFoundException) {
            $currentLocale = $fallbackLocale;
        }

        $qb = $this->entityManager->getConnection()->createQueryBuilder();

        $qb
            ->select([
                'taxon.id as indexed',
                'taxon.id as id',
                'taxon.tree_root as tree_root',
                'taxon.parent_id as parent_id',
                'taxon.code as code',
                'taxon.tree_left as tree_left',
                'taxon.tree_right as tree_right',
                'taxon.tree_level as tree_level',
                'taxon.position as position',
                'COALESCE(current_translation.name, fallback_translation.name) as name',
            ])
            ->from('sylius_taxon', 'taxon')
            ->leftJoin(
                'taxon',
                'sylius_taxon_translation',
                'current_translation',
                $qb->expr()->and(
                    $qb->expr()->eq('current_translation.translatable_id', 'taxon.id'),
                    $qb->expr()->eq('current_translation.locale', ':currentLocale')
                )
            )
            ->leftJoin(
                'taxon',
                'sylius_taxon_translation',
                'fallback_translation',
                $qb->expr()->and(
                    $qb->expr()->eq('fallback_translation.translatable_id', 'taxon.id'),
                    $qb->expr()->eq('fallback_translation.locale', ':fallbackLocale')
                )
            )
            ->orderBy('taxon.tree_level', Criteria::DESC)
            ->addOrderBy('taxon.position', Criteria::ASC)
            ->setParameter('currentLocale', $currentLocale, Types::STRING)
            ->setParameter('fallbackLocale', $fallbackLocale, Types::STRING)
        ;

        return $qb->executeQuery()->fetchAllAssociativeIndexed();
    }
}
