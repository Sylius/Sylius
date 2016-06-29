<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Core\Model;

use Sylius\Addressing\Model\AddressInterface as BaseAddressInterface;
use Sylius\User\Model\CustomerAwareInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface AddressInterface extends BaseAddressInterface, CustomerAwareInterface
{
}
