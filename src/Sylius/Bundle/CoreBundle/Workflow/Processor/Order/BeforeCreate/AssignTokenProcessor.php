<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Processor\Order\BeforeCreate;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\TokenAssigner\OrderTokenAssignerInterface;

final class AssignTokenProcessor implements BeforeOrderCreateProcessorInterface
{
    public function __construct(private OrderTokenAssignerInterface $orderTokenAssigner)
    {
    }

    public function process(OrderInterface $order): void
    {
        $this->orderTokenAssigner->assignTokenValue($order);
    }
}
