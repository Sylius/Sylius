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

namespace Sylius\Bundle\TaxonomyBundle\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

/**
 * @template T of TaxonInterface
 *
 * @implements TaxonRepositoryInterface<T>
 */
class TaxonRepository extends EntityRepository implements TaxonRepositoryInterface
{
    public function findChildren(string $parentCode, ?string $locale = null): array
    {
        return $this->createTranslationBasedQueryBuilder($locale)
            ->addSelect('child')
            ->innerJoin('o.parent', 'parent')
            ->leftJoin('o.children', 'child')
            ->andWhere('parent.code = :parentCode')
            ->addOrderBy('o.position')
            ->setParameter('parentCode', $parentCode)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findChildrenByChannelMenuTaxon(?TaxonInterface $menuTaxon = null, ?string $locale = null): array
    {
        $hydrationQuery = $this->createTranslationBasedQueryBuilder($locale)
            ->addSelect('o')
            ->addSelect('oc')
            ->leftJoin('o.children', 'oc')
        ;

        if (null !== $menuTaxon) {
            $hydrationQuery
                ->andWhere('o.root = :root')
                ->setParameter('root', $menuTaxon)
            ;
        }

        $hydrationQuery->getQuery()->getResult();

        return $this->createTranslationBasedQueryBuilder($locale)
            ->addSelect('child')
            ->innerJoin('o.parent', 'parent')
            ->leftJoin('o.children', 'child')
            ->andWhere('o.enabled = :enabled')
            ->andWhere('parent.code = :parentCode')
            ->addOrderBy('o.position')
            ->setParameter('parentCode', ($menuTaxon !== null) ? $menuTaxon->getCode() : 'category')
            ->setParameter('enabled', true)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneBySlug(string $slug, string $locale): ?TaxonInterface
    {
        return $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->innerJoin('o.translations', 'translation')
            ->andWhere('o.enabled = :enabled')
            ->andWhere('translation.slug = :slug')
            ->andWhere('translation.locale = :locale')
            ->setParameter('slug', $slug)
            ->setParameter('locale', $locale)
            ->setParameter('enabled', true)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByName(string $name, string $locale): array
    {
        return $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->innerJoin('o.translations', 'translation')
            ->andWhere('translation.name = :name')
            ->andWhere('translation.locale = :locale')
            ->setParameter('name', $name)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findRootNodes(): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.parent IS NULL')
            ->addOrderBy('o.position')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findHydratedRootNodes(): array
    {
        $this->createQueryBuilder('o')
            ->select(['o', 'oc', 'ot'])
            ->leftJoin('o.children', 'oc')
            ->leftJoin('o.translations', 'ot')
            ->getQuery()
            ->getResult()
        ;

        return $this->findRootNodes();
    }

    public function findByNamePart(string $phrase, ?string $locale = null, ?int $limit = null): array
    {
        $subqueryBuilder = $this->createQueryBuilder('sq')
            ->innerJoin('sq.translations', 'translation', 'WITH', 'translation.name LIKE :name')
            ->groupBy('sq.id')
            ->addGroupBy('translation.translatable')
            ->orderBy('translation.translatable', 'DESC')
        ;

        $queryBuilder = $this->createQueryBuilder('o');

        /** @var TaxonInterface[] $results */
        $results = $queryBuilder
            ->andWhere($queryBuilder->expr()->in('o', $subqueryBuilder->getDQL()))
            ->setParameter('name', '%' . $phrase . '%')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;

        foreach ($results as $result) {
            $result->setFallbackLocale(array_key_first($result->getTranslations()->toArray()));
        }

        return $results;
    }

    public function createListQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('o')->leftJoin('o.translations', 'translation');
    }

    protected function createTranslationBasedQueryBuilder(?string $locale): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->leftJoin('o.translations', 'translation')
        ;

        if (null !== $locale) {
            $queryBuilder
                ->andWhere('translation.locale = :locale')
                ->setParameter('locale', $locale)
            ;
        }

        return $queryBuilder;
    }
}
