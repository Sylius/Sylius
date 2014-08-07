<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Pagerfanta\Pagerfanta;

/**
 * Model repository interface.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface RepositoryInterface extends ObjectRepository
{
    /**
     * Get paginated collection.
     *
     * @param null|array $criteria
     * @param null|array $orderBy
     *
     * @return Pagerfanta
     */
    public function createPaginator(array $criteria = null, array $orderBy = null);
}
