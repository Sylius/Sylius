<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\VariableProductBundle\Doctrine\ORM;

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
            ->join($this->getAlias().'.product', 'p')
            ->addSelect('p')
            ->leftJoin($this->getAlias().'.options', 'o')
            ->addSelect('o')
        ;
    }

    protected function getAlias()
    {
        return 'v';
    }
}
