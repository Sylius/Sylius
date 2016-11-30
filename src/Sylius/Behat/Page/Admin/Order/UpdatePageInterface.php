<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Order;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;
use Sylius\Component\Addressing\Model\AddressInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface UpdatePageInterface extends BaseUpdatePageInterface
{
    /**
     * @param AddressInterface $address
     */
    public function specifyShippingAddress(AddressInterface $address);

    /**
     * @param AddressInterface $address
     */
    public function specifyBillingAddress(AddressInterface $address);
}
