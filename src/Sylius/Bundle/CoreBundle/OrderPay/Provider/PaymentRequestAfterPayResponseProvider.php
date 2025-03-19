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

use Sylius\Bundle\CoreBundle\OrderPay\Handler\PaymentStateFlashHandlerInterface;
use Sylius\Bundle\PaymentBundle\Processor\HttpResponseProcessorInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\Factory\PaymentRequestFactoryInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Webmozart\Assert\Assert;

/** @experimental */
final class PaymentRequestAfterPayResponseProvider implements AfterPayResponseProviderInterface
{
    /**
     * @param PaymentRequestFactoryInterface<PaymentRequestInterface> $paymentRequestFactory
     * @param PaymentRequestRepositoryInterface<PaymentRequestInterface> $paymentRequestRepository
     */
    public function __construct(
        private PaymentRequestFactoryInterface $paymentRequestFactory,
        private HttpResponseProcessorInterface $httpResponseProcessor,
        private PaymentRequestRepositoryInterface $paymentRequestRepository,
        private PaymentStateFlashHandlerInterface $paymentStateFlashHandler,
        private FinalUrlProviderInterface $orderPayFinalUrlProvider,
    ) {
    }

    public function getResponse(RequestConfiguration $requestConfiguration): Response
    {
        $hash = $this->getPaymentRequestHash($requestConfiguration);
        Assert::notNull($hash, 'A request attribute "hash" is required to retrieve the related order.');

        /** @var PaymentRequestInterface|null $previousPaymentRequest */
        $previousPaymentRequest = $this->paymentRequestRepository->find($hash);
        if (null === $previousPaymentRequest) {
            throw new NotFoundHttpException(sprintf('The Payment Request with hash "%s" does not exist.', $hash));
        }

        $paymentRequest = $this->paymentRequestFactory->createFromPaymentRequest($previousPaymentRequest);
        $paymentRequest->setAction(PaymentRequestInterface::ACTION_STATUS);

        $this->paymentRequestRepository->add($paymentRequest);

        $response = $this->httpResponseProcessor->process($requestConfiguration, $paymentRequest);

        /** @var PaymentInterface $payment */
        $payment = $paymentRequest->getPayment();
        $this->paymentStateFlashHandler->handle($requestConfiguration, $payment->getState());

        return $response ?? new RedirectResponse($this->orderPayFinalUrlProvider->getUrl($payment));
    }

    public function supports(RequestConfiguration $requestConfiguration): bool
    {
        return null !== $this->getPaymentRequestHash($requestConfiguration);
    }

    private function getPaymentRequestHash(RequestConfiguration $requestConfiguration): mixed
    {
        return $requestConfiguration->getRequest()->attributes->get('hash');
    }
}
