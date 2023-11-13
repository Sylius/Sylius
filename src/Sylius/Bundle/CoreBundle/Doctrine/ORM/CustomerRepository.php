<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM;

use Sylius\Bundle\CustomerBundle\Doctrine\ORM\CustomerRepository as BaseCustomerRepository;
use Sylius\Component\Core\Model\CustomerInterface;

/**
 * @template T of CustomerInterface
 *
 * @extends BaseCustomerRepository<T>
 */
class CustomerRepository extends BaseCustomerRepository
{

}
