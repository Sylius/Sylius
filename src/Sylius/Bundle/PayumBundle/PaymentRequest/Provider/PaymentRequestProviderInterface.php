<?php

declare(strict_types=1);

namespace Sylius\Bundle\PayumBundle\PaymentRequest\Provider;

use Sylius\Component\Payment\Model\PaymentRequestInterface;

interface PaymentRequestProviderInterface
{
    public function provideFromHash(string $hash): ?PaymentRequestInterface;
}
