<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Command\Payment\Payum;

class PayumCapture
{
    public function __construct(
        public string $hash,
    ) {
    }
}
