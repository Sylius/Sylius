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
use Sylius\Bundle\CoreBundle\PaymentRequest\Announcer\PaymentRequestAnnouncerInterface;
use Sylius\Bundle\CoreBundle\PaymentRequest\Provider\ServiceProviderAwareProviderInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Payment\Factory\PaymentRequestFactoryInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class PaymentRequestPayResponseProvider implements PayResponseProviderInterface
{
    /**
     * @param PaymentRequestFactoryInterface<PaymentRequestInterface> $paymentRequestFactory
     * @param PaymentRequestRepositoryInterface<PaymentRequestInterface> $paymentRequestRepository
     */
    public function __construct(
        private PaymentRequestFactoryInterface $paymentRequestFactory,
        private PaymentRequestRepositoryInterface $paymentRequestRepository,
        private PaymentRequestAnnouncerInterface $paymentRequestAnnouncer,
        private ServiceProviderAwareProviderInterface $httpResponseProvider,
        private PaymentToPayResolverInterface $paymentToPayResolver,
        private AfterPayUrlProvider $afterPayUrlProvider,
    ) {
    }

    public function getResponse(
        RequestConfiguration $requestConfiguration,
        OrderInterface $order
    ): Response {
        $payment = $this->paymentToPayResolver->getLastPayment($order);
        Assert::notNull($payment, sprintf('Order (id %s) must have last payment in state "new".', $order->getId()));

        $paymentMethod = $payment->getMethod();
        Assert::notNull($paymentMethod, sprintf('Payment (id %s) must have payment method.', $payment->getId()));

        $paymentRequest = $this->paymentRequestFactory->create($payment, $paymentMethod);

        $action = $paymentRequest->getAction();
        if ($paymentMethod->getGatewayConfig()?->getConfig()['use_authorize'] ?? false) {
            $action = PaymentRequestInterface::ACTION_AUTHORIZE;
        }
        $paymentRequest->setAction($action);

        $this->paymentRequestRepository->add($paymentRequest);

        $this->paymentRequestAnnouncer->dispatchPaymentRequestCommand($paymentRequest);

        if ($this->httpResponseProvider->supports($requestConfiguration, $paymentRequest)) {
            return $this->httpResponseProvider->getResponse($requestConfiguration, $paymentRequest);
        }

        return new RedirectResponse($this->afterPayUrlProvider->getUrl($paymentRequest));
    }

    public function supports(
        RequestConfiguration $requestConfiguration,
        OrderInterface $order
    ): bool {
        return true;
    }
}
