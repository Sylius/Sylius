<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\AfterPlacedOrder;

use Sylius\Bundle\CoreBundle\Workflow\Processor\Order\AfterOrderCreateProcessorInterface;
use Sylius\Component\Core\Customer\OrderAddressesSaverInterface;
use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperatorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Promotion\Modifier\OrderPromotionsUsageModifierInterface;

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
