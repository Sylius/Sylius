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

/**
 * Model repository interface.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface RepositoryInterface extends ObjectRepository
{
    /**
     * Get paginated collection
     *
     * @param array $criteria
     * @param array $orderBy
     *
     * @return mixed
     */
    public function createPaginator(array $criteria = null, array $orderBy = null);
}
