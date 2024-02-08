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

namespace Sylius\Behat\Page\Admin\Order;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;
use Sylius\Component\Addressing\Model\AddressInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    public function specifyShippingAddress(AddressInterface $address): void;

    public function specifyBillingAddress(AddressInterface $address): void;

    public function checkValidationMessageFor(string $element, string $message): bool;
}
