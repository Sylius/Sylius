<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\StoreBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * Default product repository.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class StoreRepository extends EntityRepository
{


    /**
     * {@inheritdoc}
     */
    public function findStoreByUser($user)
    {
        $queryBuilder = $this->getQueryBuilder();

        return $queryBuilder
            ->where(array(
                'user_id' => $user
            ))
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAlias()
    {
        return 'store';
    }
}
