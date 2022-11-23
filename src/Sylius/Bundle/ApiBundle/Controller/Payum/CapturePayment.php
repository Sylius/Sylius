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
use Payum\Core\Request\Capture;
use Sylius\Bundle\ApiBundle\Converter\PayumReplyConverterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/** @experimental */
final class CapturePayment
{
    public function __construct(
        private Payum $payum,
        private PayumReplyConverterInterface $payumReplyConverter,
    ) {
    }

    /**
     * This controller is design to reproduce what Payum `CaptureController` is doing
     * If the Capture is not triggering a `Reply` then invalidate the token and return
     * the Token's after url else return the reply and token info.
     *
     * @see \Payum\Bundle\PayumBundle\Controller\CaptureController::doAction
     */
    public function __invoke(Request $request): JsonResponse
    {
        $token = $this->payum->getHttpRequestVerifier()->verify($request);

        $gateway = $this->payum->getGateway($token->getGatewayName());

        $captureRequest = new Capture($token);

        $reply = $gateway->execute($captureRequest, true);

        if (null === $reply) {
            $this->payum->getHttpRequestVerifier()->invalidate($token);

            $reply = new HttpRedirect($token->getTargetUrl());
        }

        return $this->payumReplyConverter->convert($reply);
    }
}
