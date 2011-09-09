<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\EventDispatcher;

final class SyliusAddressingEvents
{
    const ADDRESS_CREATE = 'sylius_addressing.event.address.create';
    const ADDRESS_UPDATE = 'sylius_addressing.event.address.update';
    const ADDRESS_DELETE = 'sylius_addressing.event.address.delete';
}
