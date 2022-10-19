<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow;

use Sylius\Component\Core\Model\OrderInterface;

interface ProcessorInterface
{
    public function process(OrderInterface $order): void;
}
