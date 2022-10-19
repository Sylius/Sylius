<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Reactor\AfterCompletedCheckout;

use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Workflow\WorkflowInterface;

final class CreateOrderReactor implements AfterCompletedCheckoutReactorInterface
{
    public function __construct(private WorkflowInterface $syliusOrderWorkflow,)
    {
    }

    public function react(OrderInterface $order): void
    {
        $this->syliusOrderWorkflow->apply($order, 'create');
    }
}
