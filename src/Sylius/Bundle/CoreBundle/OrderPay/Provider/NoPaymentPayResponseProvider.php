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
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class NoPaymentPayResponseProvider implements PayResponseProviderInterface
{
    public function __construct(
        private PaymentToPayResolverInterface $paymentToPayResolver,
        private FinalUrlProviderInterface $orderPayFinalUrlProvider,
    ) {
    }

    public function getResponse(Request $request, OrderInterface $order): Response
    {
        return new RedirectResponse($this->orderPayFinalUrlProvider->getUrl(null));
    }

    public function supports(Request $request, OrderInterface $order): bool
    {
        return null === $this->paymentToPayResolver->getPayment($order);
    }
}
