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

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\Customer;
use Webmozart\Assert\Assert;

final class CustomerDefaultAddressListener
{
    public function preCreate(ResourceControllerEvent $event): void
    {
        $address = $event->getSubject();

        /** @var AddressInterface $address */
        Assert::isInstanceOf($address, AddressInterface::class);

        $this->setAddressAsDefault($address);
    }

    private function setAddressAsDefault(AddressInterface $address): void
    {
        if (null !== $address->getId()) {
            return;
        }

        /** @var Customer|null $customer */
        $customer = $address->getCustomer();

        if (null !== $customer && null === $customer->getDefaultAddress()) {
            $customer->setDefaultAddress($address);
        }
    }
}
