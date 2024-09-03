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

namespace Sylius\Bundle\ShopBundle\Provider;

use Sylius\Bundle\CoreBundle\PaymentRequest\Announcer\PaymentRequestAnnouncerInterface;
use Sylius\Bundle\CoreBundle\PaymentRequest\Provider\ServiceProviderAwareProviderInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ShopBundle\Resolver\PaymentToPayResolverInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Payment\Factory\PaymentRequestFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

use function sprintf;

final class PaymentRequestPayResponseProvider implements PayResponseProviderInterface
{
    public function __construct(
        private PaymentToPayResolverInterface $paymentToPayResolver,
        private PaymentRequestFactoryInterface $paymentRequestFactory,
        private PaymentRequestAnnouncerInterface $paymentRequestAnnouncer,
        private ServiceProviderAwareProviderInterface $httpResponseProvider,
        private RouterInterface $router,
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

        $this->paymentRequestAnnouncer->dispatchPaymentRequestCommand($paymentRequest);

        if ($this->httpResponseProvider->supports($requestConfiguration, $paymentRequest)) {
            return $this->httpResponseProvider->getResponse($requestConfiguration, $paymentRequest);
        }

        $url = $this->router->generate('sylius_shop_order_after_pay', [
            'tokenValue' => $order->getTokenValue(),
        ]);

        return new RedirectResponse($url);
    }

    public function supports(
        RequestConfiguration $requestConfiguration,
        OrderInterface $order
    ): bool {
        return true;
    }
}
