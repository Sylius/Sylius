<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    protected function getQueryBuilder()
    {
        return parent::getQueryBuilder()
            ->leftJoin('u.addresses', 'a')       ->addSelect('a')
            ->leftJoin('a.country', 'c')         ->addSelect('c')
            ->leftJoin('u.billingAddress', 'ba') ->addSelect('ba')
            ->leftJoin('u.shippingAddress', 'sa')->addSelect('sa')
        ;
    }

    protected function getAlias()
    {
        return 'u';
    }
}
