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

namespace Sylius\Bundle\AdminBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class PaymentMethodExtension extends AbstractExtension
{
    /**
     * @param array<string, string> $gatewayFactories
     * @param array<string> $excludedGatewayFactories
     */
    public function __construct(
        private readonly array $gatewayFactories,
        private readonly array $excludedGatewayFactories = [],
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_admin_get_payment_gateways', [$this, 'getPaymentGateways']),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function getPaymentGateways(): array
    {
        return array_filter(
            $this->gatewayFactories,
            fn (string $gatewayFactory) => !in_array($gatewayFactory, $this->excludedGatewayFactories, true),
            \ARRAY_FILTER_USE_KEY,
        );
    }
}
