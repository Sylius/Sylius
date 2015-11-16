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
use Sylius\Component\Taxonomy\Repository\TaxonomyRepositoryInterface;

/**
 * Base taxonomy repository.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class TaxonomyRepository extends TranslatableResourceRepository implements TaxonomyRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findOneByName($name)
    {
        $queryBuilder = $this->objectRepository->createQueryBuilder('o');

        return $queryBuilder
            ->addSelect('translation')
            ->leftJoin('o.translations', 'translation')
            ->andWhere('translation.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
