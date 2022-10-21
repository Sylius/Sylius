<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\Order;

use Sylius\Bundle\CoreBundle\Workflow\Processor\Order\AfterOrderCreateProcessorInterface;
use Sylius\Component\Core\Customer\OrderAddressesSaverInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class SaveAddressesOnCustomerCallback implements AfterPlacedOrderCallbackInterface
{
    public function __construct(private OrderAddressesSaverInterface $addressesSaver)
    {
    }

    public function call(OrderInterface $order): void
    {
        $this->addressesSaver->saveAddresses($order);
    }
}
