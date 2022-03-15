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

namespace Sylius\Bundle\TaxonomyBundle\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

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

    public function findByNamePart(string $phrase, ?string $locale = null, ?int $limit = null): array
    {
        /** @var TaxonInterface[] $results */
        $results = $this->createTranslationBasedQueryBuilder($locale)
            ->andWhere('translation.name LIKE :name')
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
