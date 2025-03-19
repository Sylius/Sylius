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

use Sylius\Bundle\CoreBundle\OrderPay\Resolver\PaymentToPayResolverInterface;
use Sylius\Bundle\PaymentBundle\Checker\FinalizedPaymentRequestCheckerInterface;
use Sylius\Bundle\PaymentBundle\Provider\DefaultActionProviderInterface;
use Sylius\Bundle\PaymentBundle\Provider\DefaultPayloadProviderInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Payment\Factory\PaymentRequestFactoryInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

/** @experimental */
final class PaymentRequestPayResponseProvider implements PayResponseProviderInterface
{
    /**
     * @param PaymentRequestFactoryInterface<PaymentRequestInterface> $paymentRequestFactory
     * @param PaymentRequestRepositoryInterface<PaymentRequestInterface> $paymentRequestRepository
     */
    public function __construct(
        private PaymentRequestFactoryInterface $paymentRequestFactory,
        private PaymentRequestRepositoryInterface $paymentRequestRepository,
        private DefaultActionProviderInterface $defaultActionProvider,
        private DefaultPayloadProviderInterface $defaultPayloadProvider,
        private FinalizedPaymentRequestCheckerInterface $finalizedPaymentRequestChecker,
        private PaymentToPayResolverInterface $paymentToPayResolver,
        private UrlProviderInterface $paymentRequestPayUrlProvider,
    ) {
    }

    public function getResponse(RequestConfiguration $requestConfiguration, OrderInterface $order): Response
    {
        $payment = $this->paymentToPayResolver->getPayment($order);
        Assert::notNull($payment, sprintf('Order (id %s) must have last payment in state "new".', $order->getId()));

        $paymentMethod = $payment->getMethod();
        Assert::notNull($paymentMethod, sprintf('Payment (id %s) must have payment method.', $payment->getId()));

        $paymentRequest = $this->getPaymentRequest($payment, $paymentMethod);
        if (null === $paymentRequest->getHash()) {
            $this->paymentRequestRepository->add($paymentRequest);
        }

        return new RedirectResponse($this->paymentRequestPayUrlProvider->getUrl($paymentRequest));
    }

    public function supports(RequestConfiguration $requestConfiguration, OrderInterface $order): bool
    {
        return true;
    }

    private function getPaymentRequest(
        PaymentInterface $payment,
        PaymentMethodInterface $paymentMethod,
    ): PaymentRequestInterface {
        $paymentRequest = $this->paymentRequestFactory->create($payment, $paymentMethod);
        $action = $this->defaultActionProvider->getAction($paymentRequest);
        $paymentRequest->setAction($action);

        $existingPaymentRequest = $this->paymentRequestRepository->findOneByActionPaymentAndMethod(
            $action,
            $payment,
            $paymentMethod,
        );

        if (null === $existingPaymentRequest || $this->finalizedPaymentRequestChecker->isFinal($existingPaymentRequest)) {
            $paymentRequest->setPayload($this->defaultPayloadProvider->getPayload($paymentRequest));

            return $paymentRequest;
        }

        return $existingPaymentRequest;
    }
}
