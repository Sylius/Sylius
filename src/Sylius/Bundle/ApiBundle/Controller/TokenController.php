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

use FOS\RestBundle\Decoder\DecoderProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;
use OAuth2\OAuth2;
use OAuth2\OAuth2ServerException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
     * @var DecoderProviderInterface
     */
    private $decoderProvider;

    /**
     * @param OAuth2 $server
     * @param DecoderProviderInterface $decoderProvider
     */
    public function __construct(OAuth2 $server, DecoderProviderInterface $decoderProvider)
    {
        $this->server = $server;
        $this->decoderProvider = $decoderProvider;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function tokenAction(Request $request)
    {
        $this->convertContentToRequestParameters($request);

        try {
            return $this->server->grantAccessToken($request);
        } catch (OAuth2ServerException $e) {
            return $e->getHttpResponse();
        }
    }

    /**
     * Additional action to keep default behaviour of FosRestBundle. Main aim is to turn off data normalization.
     *
     * @param Request $request
     */
    private function convertContentToRequestParameters(Request $request)
    {
        $format = $request->getFormat($request->headers->get('Content-Type', '')) ?: $request->getRequestFormat();
        $content = $request->getContent();

        if (empty($content)) {
            return;
        }

        if (!$this->decoderProvider->supports($format)) {
            return;
        }

        $decoder = $this->decoderProvider->getDecoder($format);
        $data = $decoder->decode($content);

        if (!is_array($data)) {
            throw new BadRequestHttpException(sprintf('Invalid %s message received', $format));
        }

        $request->request = new ParameterBag($data);
    }
}
