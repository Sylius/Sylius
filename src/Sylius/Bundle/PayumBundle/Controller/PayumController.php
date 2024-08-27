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

namespace Sylius\Bundle\PayumBundle\Controller;

use Payum\Core\Payum;
use Payum\Core\Request\Generic;
use Payum\Core\Request\GetStatusInterface;
use Payum\Core\Security\HttpRequestVerifierInterface;
use Sylius\Bundle\PayumBundle\Factory\GetStatusFactoryInterface;
use Sylius\Bundle\PayumBundle\Factory\ResolveNextRouteFactoryInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

final class PayumController
{
    public function __construct(
        private Payum $payum,
        private RouterInterface $router,
        private GetStatusFactoryInterface $getStatusRequestFactory,
        private ResolveNextRouteFactoryInterface $resolveNextRouteRequestFactory,
    ) {
    }

    public function afterCaptureAction(Request $request): Response
    {
        $token = $this->getHttpRequestVerifier()->verify($request);

        /** @var Generic&GetStatusInterface $status */
        $status = $this->getStatusRequestFactory->createNewWithModel($token);
        $this->payum->getGateway($token->getGatewayName())->execute($status);

        $resolveNextRoute = $this->resolveNextRouteRequestFactory->createNewWithModel($status->getFirstModel());
        $this->payum->getGateway($token->getGatewayName())->execute($resolveNextRoute);

        $this->getHttpRequestVerifier()->invalidate($token);

        if (PaymentInterface::STATE_NEW !== $status->getValue()) {
            /** @var FlashBagInterface $flashBag */
            $flashBag = $request->getSession()->getBag('flashes');
            $flashBag->add('info', sprintf('sylius.payment.%s', $status->getValue()));
        }

        return new RedirectResponse($this->router->generate($resolveNextRoute->getRouteName(), $resolveNextRoute->getRouteParameters()));
    }

    private function getHttpRequestVerifier(): HttpRequestVerifierInterface
    {
        return $this->payum->getHttpRequestVerifier();
    }
}
