<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ApiBundle\Security\EntryPoint;

use OAuth2\OAuth2;
use Sylius\Bundle\ApiBundle\OAuth2AuthenticateException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

/**
 * @see \FOS\OAuthServerBundle\Security\EntryPoint\OAuthEntryPoint
 *
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class OAuthEntryPoint implements AuthenticationEntryPointInterface
{
    /**
     * @var OAuth2
     */
    private $serverService;

    /**
     * @param OAuth2 $serverService
     */
    public function __construct(OAuth2 $serverService)
    {
        $this->serverService = $serverService;
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $exception = new OAuth2AuthenticateException(
            Response::HTTP_UNAUTHORIZED,
            OAuth2::TOKEN_TYPE_BEARER,
            $this->serverService->getVariable(OAuth2::CONFIG_WWW_REALM),
            'access_denied',
            'OAuth2 authentication required'
        );

        return $exception->getHttpResponse();
    }
}
