<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Repository;

use Pagerfanta\PagerfantaInterface;
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface as BaseUserRepositoryInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
interface UserRepositoryInterface extends BaseUserRepositoryInterface
{
    /**
     * @param array $criteria
     * @param array $sorting
     *
     * @return PagerfantaInterface
     */
    public function createFilterPaginator(array $criteria = null, array $sorting = null);

    /**
     * @param mixed $id
     *
     * @return UserInterface|null
     */
    public function findForDetailsPage($id);
}
