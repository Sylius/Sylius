<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\User\Repository;

use Pagerfanta\Pagerfanta;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface CustomerRepositoryInterface extends RepositoryInterface
{
    /**
     * @return int
     */
    public function count();

    /**
     * @param int $count
     *
     * @return CustomerInterface[]
     */
    public function findLatest($count);

    /**
     * @param mixed $id
     *
     * @return null|CustomerInterface
     */
    public function findForDetailsPage($id);

    /**
     * @param array $criteria
     * @param array $sorting
     *
     * @return Pagerfanta
     */
    public function createFilterPaginator(array $criteria = null, array $sorting = null);
}
