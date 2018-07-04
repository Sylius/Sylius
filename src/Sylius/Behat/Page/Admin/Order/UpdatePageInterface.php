<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Page\Admin\Order;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;
use Sylius\Component\Addressing\Model\AddressInterface;

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

    /**
     * @param string $element
     * @param string $message
     *
     * @return bool
     */
    public function checkValidationMessageFor($element, $message);
}
