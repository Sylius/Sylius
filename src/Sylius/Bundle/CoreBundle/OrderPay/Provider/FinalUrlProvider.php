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

namespace Sylius\Bundle\CoreBundle\OrderPay\Provider;

use Sylius\Bundle\CoreBundle\OrderPay\Processor\RouteParametersProcessorInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentInterface as BasePaymentInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/** @experimental */
final class FinalUrlProvider implements FinalUrlProviderInterface
{
    /**
     * @param array<string, string> $finalRouteParameters
     * @param array<string, string> $retryRouteParameters
     */
    public function __construct(
        private RouteParametersProcessorInterface $routeParametersProcessor,
        private string $finalRoute,
        private array $finalRouteParameters,
        private string $retryRoute,
        private array $retryRouteParameters,
    ) {
    }

    public function getUrl(
        ?PaymentInterface $payment,
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH,
    ): string {
        $context = [
            'payment' => $payment,
            'order' => $payment?->getOrder(),
        ];

        if (
            null === $payment ||
            $payment->getState() === BasePaymentInterface::STATE_COMPLETED ||
            $payment->getState() === BasePaymentInterface::STATE_AUTHORIZED
        ) {
            return $this->routeParametersProcessor->process(
                $this->finalRoute,
                $this->finalRouteParameters,
                $referenceType,
                $context,
            );
        }

        return $this->routeParametersProcessor->process(
            $this->retryRoute,
            $this->retryRouteParameters,
            $referenceType,
            $context,
        );
    }
}
