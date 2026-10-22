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

namespace Sylius\Bundle\PayumBundle\OrderPay\Provider;

use Payum\Core\Payum;
use Payum\Core\Request\Generic;
use Payum\Core\Request\GetStatusInterface;
use Payum\Core\Security\HttpRequestVerifierInterface;
use Sylius\Bundle\CoreBundle\OrderPay\Handler\PaymentStateFlashHandlerInterface;
use Sylius\Bundle\CoreBundle\OrderPay\Provider\AfterPayResponseProviderInterface;
use Sylius\Bundle\PayumBundle\Factory\GetStatusFactoryInterface;
use Sylius\Bundle\PayumBundle\Factory\ResolveNextRouteFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

final class PayumAfterPayResponseProvider implements AfterPayResponseProviderInterface
{
    public function __construct(
        private Payum $payum,
        private RouterInterface $router,
        private GetStatusFactoryInterface $getStatusRequestFactory,
        private ResolveNextRouteFactoryInterface $resolveNextRouteRequestFactory,
        private PaymentStateFlashHandlerInterface $paymentStatusFlashHandler,
    ) {
    }

    public function getResponse(Request $request): Response
    {
        $token = $this->getHttpRequestVerifier()->verify($request);

        /** @var GetStatusInterface&Generic $status */
        $status = $this->getStatusRequestFactory->createNewWithModel($token);
        $this->payum->getGateway($token->getGatewayName())->execute($status);

        $resolveNextRoute = $this->resolveNextRouteRequestFactory->createNewWithModel($status->getFirstModel());
        $this->payum->getGateway($token->getGatewayName())->execute($resolveNextRoute);

        $this->paymentStatusFlashHandler->handle($request, (string) $status->getValue());

        $url = $this->router->generate(
            $resolveNextRoute->getRouteName(),
            $resolveNextRoute->getRouteParameters(),
        );

        $this->getHttpRequestVerifier()->invalidate($token);

        return new RedirectResponse($url);
    }

    public function supports(Request $request): bool
    {
        $hash = $request->attributes->get('payum_token', $request->query->get('payum_token', $request->request->get('payum_token', false)));

        return false !== $hash;
    }

    private function getHttpRequestVerifier(): HttpRequestVerifierInterface
    {
        return $this->payum->getHttpRequestVerifier();
    }
}
