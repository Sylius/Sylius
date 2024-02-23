<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\PaymentRequest\Provider;

use Sylius\Component\Core\Model\PaymentMethodInterface;

interface GatewayFactoryNameProviderInterface
{
    public function provide(PaymentMethodInterface $paymentMethod): ?string;
}
