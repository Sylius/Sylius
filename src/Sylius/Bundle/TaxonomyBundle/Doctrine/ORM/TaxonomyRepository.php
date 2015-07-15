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

/**
 * Base taxonomy repository.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class TaxonomyRepository extends TranslatableResourceRepository
{
    /**
     * {@inheritdoc}
     */
    protected function getQueryBuilder()
    {
        return parent::getQueryBuilder()
            ->select('taxonomy, root, taxons')
            ->leftJoin('taxonomy.root', 'root')
            ->leftJoin('root.children', 'taxons')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getCollectionQueryBuilder()
    {
        return parent::getQueryBuilder()
            ->select('taxonomy, root, taxons')
            ->leftJoin('taxonomy.root', 'root')
            ->leftJoin('root.children', 'taxons')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAlias()
    {
        return 'taxonomy';
    }
}
