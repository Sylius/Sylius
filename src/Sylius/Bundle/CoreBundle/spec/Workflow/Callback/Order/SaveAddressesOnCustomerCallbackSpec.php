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

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Callback\Order;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Order\AfterPlacedOrderCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Order\SaveAddressesOnCustomerCallback;
use Sylius\Component\Core\Customer\OrderAddressesSaverInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class SaveAddressesOnCustomerCallbackSpec extends ObjectBehavior
{
    function let(OrderAddressesSaverInterface $addressesSaver): void
    {
        $this->beConstructedWith($addressesSaver);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(SaveAddressesOnCustomerCallback::class);
    }

    function it_is_called_after_placed_order(): void
    {
        $this->shouldImplement(AfterPlacedOrderCallbackInterface::class);
    }

    function it_saves_addresses_on_customer(
        OrderInterface $order,
        OrderAddressesSaverInterface $addressesSaver,
    ): void
    {
        $addressesSaver->saveAddresses($order)->shouldBeCalled();

        $this->call($order);
    }
}
