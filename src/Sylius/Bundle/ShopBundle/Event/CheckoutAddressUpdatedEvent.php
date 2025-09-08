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

namespace Sylius\Bundle\ShopBundle\Event;

use ArrayObject;
use Sylius\Component\Addressing\Model\AddressInterface;
use Symfony\Contracts\EventDispatcher\Event;

class CheckoutAddressUpdatedEvent extends Event
{
    public function __construct(
        private ArrayObject $formData,
        private AddressInterface $address,
    ) {
    }

    public function getFormData(): ArrayObject
    {
        return $this->formData;
    }

    public function getAddress(): AddressInterface
    {
        return $this->address;
    }
}
