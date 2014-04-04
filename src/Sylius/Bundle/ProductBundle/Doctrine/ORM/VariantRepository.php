<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * Variant repository.
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class VariantRepository extends EntityRepository
{
    protected function getCollectionQueryBuilder()
    {
        return parent::getCollectionQueryBuilder()
            ->join($this->getAlias().'.object', 'product')
            ->addSelect('product')
            ->leftJoin($this->getAlias().'.options', 'option')
            ->addSelect('option')
        ;
    }

    protected function getAlias()
    {
        return 'variant';
    }
}

