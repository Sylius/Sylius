<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\PaymentRequest\CommandProvider;

interface ServiceProviderAwareCommandProviderInterface extends PaymentRequestCommandProviderInterface
{
    public function getCommandProvider(string $index): ?PaymentRequestCommandProviderInterface;

    /**
     * @return string[]
     */
    public function getCommandProviderIndex(): array;
}
