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

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Webmozart\Assert\Assert;

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
        $root = $taxon->isRoot() ? $taxon : $taxon->getRoot();

        $queryBuilder = $this->createQueryBuilder('o');
        $queryBuilder
            ->andWhere($queryBuilder->expr()->eq('o.root', ':root'))
            ->andWhere($queryBuilder->expr()->lt('o.right', ':right'))
            ->andWhere($queryBuilder->expr()->gt('o.left', ':left'))
            ->setParameter('root', $root)
            ->setParameter('left', $taxon->getLeft())
            ->setParameter('right', $taxon->getRight())
        ;

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findChildrenByRootCode($code)
    {
        $root = $this->findOneBy(['code' => $code]);

        Assert::notNull($root, sprintf('Taxon with code "%s" not found.', $code));

        return $this->findChildren($root);
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

    /**
     * {@inheritdoc}
     */
    public function findNodesTreeSorted()
    {
        $queryBuilder = $this->createQueryBuilder('o');
        $queryBuilder
            ->orderBy('o.root')
            ->addOrderBy('o.left')
        ;
    
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getFormQueryBuilder()
    {
        return $this->createQueryBuilder('o');
    }
}
