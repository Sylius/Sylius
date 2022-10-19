<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Processor\Order\AfterCreate;

use Sylius\Bundle\CoreBundle\Workflow\Processor\Order\AfterOrderCreateProcessorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentTransitions;
use Symfony\Component\Workflow\WorkflowInterface;

final class RequestPaymentAfterOrderCreateProcessor implements AfterOrderCreateProcessorInterface
{
    public function __construct(private WorkflowInterface $syliusOrderPaymentWorkflow)
    {
    }

    public function process(OrderInterface $order): void
    {
        $this->syliusOrderPaymentWorkflow->apply($order, OrderPaymentTransitions::TRANSITION_REQUEST_PAYMENT);
    }
}
