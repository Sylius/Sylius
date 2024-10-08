<?php

declare(strict_types=1);

namespace Sylius\Bundle\PaymentBundle\Provider;

use LogicException;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

final class CompositeWebhookPaymentProvider implements WebhookPaymentProviderInterface
{
    /**
     * @param iterable<WebhookPaymentProviderInterface> $webhookPaymentProviders
     */
    public function __construct(
        private iterable $webhookPaymentProviders,
    ) {
        Assert::allIsInstanceOf($this->webhookPaymentProviders, WebhookPaymentProviderInterface::class);
    }

    public function getPayment(Request $request, PaymentMethodInterface $paymentMethod): PaymentInterface {
        foreach ($this->webhookPaymentProviders as $provider) {
            if ($provider->supports($request, $paymentMethod)) {
                return $provider->getPayment($request, $paymentMethod);
            }
        }

        throw new LogicException(sprintf(
            'No webhook payment provider found supporting this request and payment method (code: %s).',
            $paymentMethod->getCode()
        ));
    }

    public function supports(Request $request, PaymentMethodInterface $paymentMethod): bool {
        return true;
    }
}
