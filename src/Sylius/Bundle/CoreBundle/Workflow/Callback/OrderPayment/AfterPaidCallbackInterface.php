<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\OrderPayment;

use Sylius\Component\Core\Model\OrderInterface;

interface AfterPaidCallbackInterface
{
    public function call(OrderInterface $order): void;
}
