<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Manager;

use Sylius\Component\Customer\Manager\CustomerManager as BaseCustomerManager;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class CustomerManager extends BaseCustomerManager
{
    /**
     * @var RepositoryInterface
     */
    protected $userRepository;

    public function __construct(RepositoryInterface $repository, RepositoryInterface $userRepository)
    {
        parent::__construct($repository);

        $this->userRepository = $userRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function createCustomer($email)
    {
        $user = $this->userRepository->findOneBy(array('email' => $email));
        if ($user) {
            return $user->getCustomer();
        }

        return parent::createCustomer($email);
    }
}
