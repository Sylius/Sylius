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

namespace Sylius\Bundle\ApiBundle\Converter;

use Payum\Core\Bridge\Symfony\Reply\HttpResponse as SymfonyHttpResponse;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Reply\ReplyInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * In the Payum world, The ReplyInterface is used by :
 * - Payum\Core\Reply\HttpResponse
 * - Payum\Core\Bridge\Symfony\Reply\HttpResponse
 *
 * If for some reason a Payum gateway is using another kind of class then this service
 * must be decorated by this gateway to ensure that the ReplyInterface object will
 * be converted to the right JSON array
 *
 * @experimental
 */
final class PayumReplyConverter implements PayumReplyConverterInterface
{
    public function convert(ReplyInterface $reply): JsonResponse
    {
        if ($reply instanceof SymfonyHttpResponse) {
            $reply = new HttpResponse(
                $reply->getResponse()->getContent(),
                $reply->getResponse()->getStatusCode(),
                $reply->getResponse()->headers->all(),
            );
        }

        return new JsonResponse([
            'content' => $reply->getContent(),
            'statusCode' => $reply->getStatusCode(),
            'headers' => $reply->getHeaders(),
        ]);
    }
}
