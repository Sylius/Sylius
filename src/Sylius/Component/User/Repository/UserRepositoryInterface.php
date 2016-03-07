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

use Pagerfanta\PagerfantaInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Model\UserInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface UserRepositoryInterface extends RepositoryInterface
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

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @param null|string $status
     *
     * @return int
     */
    public function countBetweenDates(\DateTime $from, \DateTime $to, $status = null);

    /**
     * @param array $configuration
     *
     * @return array
     */
    public function getRegistrationStatistic(array $configuration = []);

    /**
     * @param string $email
     *
     * @return UserInterface|null
     */
    public function findOneByEmail($email);
}
