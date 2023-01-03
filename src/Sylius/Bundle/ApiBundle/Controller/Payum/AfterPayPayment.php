<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Controller\Payum;

use Payum\Core\Payum;
use Payum\Core\Reply\HttpRedirect;
use Sylius\Bundle\ApiBundle\Converter\PayumReplyConverterInterface;
use Sylius\Bundle\PayumBundle\Factory\GetStatusFactoryInterface;
use Sylius\Bundle\PayumBundle\Factory\ResolveNextRouteFactoryInterface;
use Sylius\Bundle\PayumBundle\Model\PaymentSecurityTokenInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

/** @experimental */
final class AfterPayPayment
{
    public function __construct(
        private Payum $payum,
        private GetStatusFactoryInterface $getStatusFactory,
        private ResolveNextRouteFactoryInterface $resolveNextRouteFactory,
        private RouterInterface $router,
        private PayumReplyConverterInterface $payumReplyConverter,
    ) {
    }

    /**
     * This controller is design to reproduce what SyliusPayumBundle `PayumController` is doing.
     * The only difference is that here we `catchReply=true`, meaning if a ReplyInterface is thrown
     * then it is caught to convert the $reply to a JSON object.
     *
     * If the "GetStatus" Payum request is not triggering a `Reply` (but return `null`) then
     * invalidate the token and return a redirect reply to the "Token's after url"
     * else return the "Reply" itself.
     *
     * @see \Sylius\Bundle\PayumBundle\Controller\PayumController::afterCaptureAction
     */
    public function __invoke(Request $request): JsonResponse
    {
        /** @var PaymentSecurityTokenInterface $token */
        $token = $this->payum->getHttpRequestVerifier()->verify($request);

        $gateway = $this->payum->getGateway($token->getGatewayName());

        $statusRequest = $this->getStatusFactory->createNewWithModel($token);

        $reply = $gateway->execute($statusRequest, true);

        if (null === $reply) {
            $this->payum->getHttpRequestVerifier()->invalidate($token);
        } else {
            return $this->payumReplyConverter->convert($reply);
        }

        $resolveNextRouteRequest = $this->resolveNextRouteFactory->createNewWithModel($statusRequest->getFirstModel());
        $reply = $gateway->execute($resolveNextRouteRequest, true);

        if (null === $reply) {
            $reply = new HttpRedirect($this->router->generate(
                $resolveNextRouteRequest->getRouteName(),
                $resolveNextRouteRequest->getRouteParameters()
            ));
        }

        return $this->payumReplyConverter->convert($reply);
    }
}
