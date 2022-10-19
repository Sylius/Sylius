<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Reactor\AfterPlacedOrder;

use Sylius\Bundle\CoreBundle\Workflow\Processor\Order\AfterOrderCreateProcessorInterface;
use Sylius\Component\Core\Customer\OrderAddressesSaverInterface;
use Sylius\Component\Core\Inventory\Operator\OrderInventoryOperatorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Promotion\Modifier\OrderPromotionsUsageModifierInterface;

final class SaveAddressesOnCustomerReactor implements AfterPlacedOrderReactorInterface
{
    public function __construct(private OrderAddressesSaverInterface $addressesSaver)
    {
    }

    public function react(OrderInterface $order): void
    {
        $this->addressesSaver->saveAddresses($order);
    }
}
