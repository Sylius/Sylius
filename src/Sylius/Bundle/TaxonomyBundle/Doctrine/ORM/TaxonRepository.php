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

use Sylius\Component\Taxonomy\Model\TaxonInterface;
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
    public function findChildren(TaxonInterface $taxon)
    {
        $queryBuilder = $this->createQueryBuilder('o');
        $queryBuilder
            ->andWhere($queryBuilder->expr()->gt('o.left', ':left'))
            ->andWhere($queryBuilder->expr()->lt('o.right', ':right'))
            ->setParameter('left', $taxon->getLeft())
            ->setParameter('right', $taxon->getRight())
        ;

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByPermalink($permalink)
    {
        return $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->leftJoin('o.translations', 'translation')
            ->where('translation.permalink = :permalink')
            ->setParameter('permalink', $permalink)
            ->orderBy('o.left')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByName($name)
    {
        return $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->leftJoin('o.translations', 'translation')
            ->where('translation.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findRootNodes()
    {
        $queryBuilder = $this->createQueryBuilder('o');
        $queryBuilder
            ->andWhere($queryBuilder->expr()->isNull($this->getPropertyName('parent')))
        ;

        return $queryBuilder->getQuery()->getResult();
    }
}
