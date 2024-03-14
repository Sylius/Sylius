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
     */
    public function __construct(private readonly array $gatewayFactories)
    {
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
        return $this->gatewayFactories;
    }
}
