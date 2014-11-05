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

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\TranslatableEntityRepository;

/**
 * Default product repository.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class ProductRepository extends TranslatableEntityRepository
{
    /**
     * {@inheritdoc}
     */
    protected function getQueryBuilder()
    {
        return parent::getQueryBuilder()
            ->select($this->getAlias().', option, variant')
            ->leftJoin($this->getAlias().'.options', 'option')
            ->leftJoin($this->getAlias().'.variants', 'variant')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAlias()
    {
        return 'product';
    }
}
