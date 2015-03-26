<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Customer\Manager;

use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class CustomerManager
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Find or create new customer entity.
     *
     * @param string $email
     *
     * @return CustomerInterface
     */
    public function createCustomer($email)
    {
        $customer = $this->repository->findOneBy(array('email' => $email));
        if (!$customer) {
            $customer = $this->repository->createNew();
        }

        return $customer;
    }
}
