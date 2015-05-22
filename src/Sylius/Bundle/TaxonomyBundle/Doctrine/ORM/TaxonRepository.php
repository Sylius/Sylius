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
use Sylius\Component\Taxonomy\Model\TaxonomyInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

/**
 * Base taxon repository.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class TaxonRepository extends TranslatableResourceRepository implements TaxonRepositoryInterface
{
    public function getTaxonsAsList(TaxonomyInterface $taxonomy)
    {
        return $this->getQueryBuilder()
            ->where('o.taxonomy = :taxonomy')
            ->andWhere('o.parent IS NOT NULL')
            ->setParameter('taxonomy', $taxonomy)
            ->orderBy('o.left')
            ->getQuery()
            ->getResult();
    }

    public function findOneByPermalink($permalink)
    {
        return $this->getQueryBuilder()
            ->where('translation.permalink = :permalink')
            ->setParameter('permalink', $permalink)
            ->orderBy($this->getAlias() . '.left')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
