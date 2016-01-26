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

use Sylius\Bundle\TranslationBundle\Doctrine\ORM\TranslatableResourceRepository;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

/**
 * @author Aram Alipoor <aram.alipoor@gmail.com>
 */
class TaxonRepository extends TranslatableResourceRepository implements TaxonRepositoryInterface
{
    /**
     * @param TaxonInterface $taxon
     *
     * @return array
     */
    public function findChildren(TaxonInterface $taxon)
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder
            ->andWhere($queryBuilder->expr()->gt('o.left', ':left'))
            ->andWhere($queryBuilder->expr()->lt('o.right', ':right'))
            ->setParameter('left', $taxon->getLeft())
            ->setParameter('right', $taxon->getRight())
        ;

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param string $permalink
     * @return TaxonInterface
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByPermalink($permalink)
    {
        return $this->getQueryBuilder()
            ->where('translation.permalink = :permalink')
            ->setParameter('permalink', $permalink)
            ->orderBy('o.left')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return array
     */
    public function findRootNodes()
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder
            ->andWhere($queryBuilder->expr()->isNull($this->getPropertyName('parent')))
        ;

        return $queryBuilder->getQuery()->getResult();
    }
}