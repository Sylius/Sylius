<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxonomyBundle\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

/**
 * @author Aram Alipoor <aram.alipoor@gmail.com>
 */
class TaxonRepository extends EntityRepository implements TaxonRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findChildren($parentCode, $locale = null)
    {
        $qb = $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->addSelect('child')
            ->innerJoin('o.parent', 'parent')
            ->leftJoin('o.children', 'child')
            ->andWhere('parent.code = :parentCode')
            ->addOrderBy('o.position')
            ->setParameter('parentCode', $parentCode)
        ;

        $qb = $this->createTranslationQueryBuilderPart($qb, $locale);

        return $qb->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBySlug($slug, $locale)
    {
        return $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->innerJoin('o.translations', 'translation')
            ->andWhere('translation.slug = :slug')
            ->andWhere('translation.locale = :locale')
            ->setParameter('slug', $slug)
            ->setParameter('locale', $locale)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findByName($name, $locale)
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

    /**
     * {@inheritdoc}
     */
    public function findRootNodes()
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.parent IS NULL')
            ->addOrderBy('o.position')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findByNamePart($phrase, $locale = null)
    {
        $qb = $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->andWhere('translation.name LIKE :name')
            ->setParameter('name', '%'.$phrase.'%')
        ;

        $qb = $this->createTranslationQueryBuilderPart($qb, $locale);

        return $qb->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function createListQueryBuilder()
    {
        return $this->createQueryBuilder('o')->leftJoin('o.translations', 'translation');
    }

    /**
     * @param QueryBuilder $qb
     * @param null $locale
     * @return QueryBuilder
     */
    private function createTranslationQueryBuilderPart(QueryBuilder $qb, $locale = null)
    {
        if ($locale !== null) {
            $qb
                ->innerJoin('o.translations', 'translation')
                ->andWhere('translation.locale = :locale')
                ->setParameter('locale', $locale)
            ;

            return $qb;
        }

        return $qb->innerJoin('o.translations', 'translation');
    }
}
