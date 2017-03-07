<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ApiBundle\Controller;

use OAuth2\OAuth2;
use OAuth2\OAuth2ServerException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @see \FOS\OAuthServerBundle\Controller\TokenController
 * @see \FOS\RestBundle\EventListener\BodyListener
 *
 * @author Łukasz Chruściel <lchrusciel@gmail.com>
 */
final class TokenController
{
    /**
     * @var OAuth2
     */
    private $server;

    /**
     * @param OAuth2 $server
     */
    public function __construct(OAuth2 $server)
    {
        $this->server = $server;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function tokenAction(Request $request)
    {
        try {
            return $this->server->grantAccessToken($request);
        } catch (OAuth2ServerException $exception) {
            return $this->normalizeExceptionResponse($exception);
        }
    }

    /**
     * @param OAuth2ServerException $exception
     *
     * @return Response
     */
    private function normalizeExceptionResponse(OAuth2ServerException $exception)
    {
        $body = [];

        $exceptionBody = json_decode($exception->getResponseBody());
        $exceptionCode = $body['code'] = $exception->getHttpCode();
        $body['message'] = $exceptionBody['error_description'];
        $body['error'] = $exceptionBody['error'];

        return new Response(
            json_encode($body),
            $exceptionCode,
            $exception->getResponseHeaders()
        );
    }
}
