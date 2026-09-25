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

use Sylius\Bundle\AdminBundle\Provider\PaymentGatewayMethodsProviderInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class PaymentGatewayMethodsExtension extends AbstractExtension
{
    public function __construct(
        private readonly PaymentGatewayMethodsProviderInterface $paymentGatewayMethodsProvider,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_payment_gateway_methods', [$this->paymentGatewayMethodsProvider, 'getMethods']),
        ];
    }
}
