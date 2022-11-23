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

use LogicException;
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
            $response = $reply->getResponse();

            $content = $response->getContent();
            /** @var string[] $headers */
            $headers = $response->headers->all();

            $reply = new HttpResponse(
                $content === false ? '' : $content,
                $response->getStatusCode(),
                $headers,
            );
        }

        if ($reply instanceof HttpResponse) {
            return new JsonResponse([
                'content' => $reply->getContent(),
                'statusCode' => $reply->getStatusCode(),
                'headers' => $reply->getHeaders(),
            ]);
        }

        throw new LogicException(sprintf(
            'This "%s" is not an instanceof "%s", please make your gateway reply a Payum HttpResponse.',
            get_class($reply),
            HttpResponse::class
        ));
    }
}
