<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxonomiesBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Bundle\TaxonomiesBundle\Model\TaxonomyInterface;
use Sylius\Bundle\TaxonomiesBundle\Repository\TaxonRepositoryInterface;

/**
 * Base taxon repository.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class TaxonRepository extends EntityRepository implements TaxonRepositoryInterface
{
    public function getTaxonsAsList(TaxonomyInterface $taxonomy)
    {
        return $this->getQueryBuilder()
            ->where('o.taxonomy = :taxonomy')
            ->andWhere('o.parent IS NOT NULL')
            ->setParameter('taxonomy', $taxonomy)
            ->getQuery()
            ->getResult()
        ;
    }
}
