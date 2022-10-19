<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Processor\OrderCheckout\AfterComplete;

use Sylius\Bundle\CoreBundle\Workflow\Processor\OrderCheckout\AfterOrderCheckoutCompleteProcessorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Workflow\WorkflowInterface;

final class CreateOrderProcessor implements AfterOrderCheckoutCompleteProcessorInterface
{
    public function __construct(private WorkflowInterface $syliusOrderWorkflow,)
    {
    }

    public function process(OrderInterface $order): void
    {
        $this->syliusOrderWorkflow->apply($order, 'create');
    }
}
