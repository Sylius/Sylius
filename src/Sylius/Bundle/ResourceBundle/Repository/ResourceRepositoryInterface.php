<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Repository;

use Doctrine\Common\Persistence\ObjectRepository;

/**
 * Resource repository interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface ResourceRepositoryInterface extends ObjectRepository
{
    /**
     * Creates a new Pagerfanta instance to paginate the resources.
     *
     * @return PagerfantaInterface
     */
    public function createPaginator(array $criteria = array(), array $sortBy = null);
}
