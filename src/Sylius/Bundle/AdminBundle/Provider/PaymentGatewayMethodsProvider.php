<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Provider;

final readonly class PaymentGatewayMethodsProvider implements PaymentGatewayMethodsProviderInterface
{
    /**
     * @param array<string, list<string>> $methods
     * @param array<string, string> $factoryAliases
     */
    public function __construct(
        private array $methods,
        private array $factoryAliases = [],
    ) {
    }

    public function getMethods(string $gateway): array
    {
        $gateway = $this->factoryAliases[$gateway] ?? $gateway;

        return $this->methods[$gateway] ?? [];
    }
}
