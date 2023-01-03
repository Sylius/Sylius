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
     * This controller is design to reproduce what Payum `CaptureController` is doing.
     * The only difference is that here we `catchReply=true`, meaning if a ReplyInterface is thrown
     * then it is caught to convert the $reply to a JSON object.
     *
     * If the "Capture" Payum request is not triggering a `Reply` (but return `null`) then
     * invalidate the token and return a redirect reply to the "Token's after url"
     * else return the "Reply" itself.
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

            $reply = new HttpRedirect($token->getAfterUrl());
        }

        return $this->payumReplyConverter->convert($reply);
    }
}
