<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Component\Customer\Model;

use Sylius\Component\Addressing\Model\AddressInterface as BaseAddressInterface;

/**
 * Address model.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface AddressInterface extends BaseAddressInterface, CustomerAwareInterface
{
}
