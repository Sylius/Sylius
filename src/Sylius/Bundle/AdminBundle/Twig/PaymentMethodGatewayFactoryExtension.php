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

use Sylius\Component\Payment\Checker\PaymentMethodGatewayFactoryCheckerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class PaymentMethodGatewayFactoryExtension extends AbstractExtension
{
    public function __construct(
        private readonly PaymentMethodGatewayFactoryCheckerInterface $paymentMethodGatewayFactoryChecker,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_has_payment_method_with_gateway_factory', [$this, 'hasPaymentMethodWithGatewayFactory']),
        ];
    }

    public function hasPaymentMethodWithGatewayFactory(string $factoryName): bool
    {
        return $this->paymentMethodGatewayFactoryChecker->hasPaymentMethodWithGatewayFactory($factoryName);
    }
}
