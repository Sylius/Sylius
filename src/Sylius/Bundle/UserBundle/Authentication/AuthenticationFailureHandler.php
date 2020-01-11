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

namespace Sylius\Bundle\UserBundle\Authentication;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;

final class AuthenticationFailureHandler extends DefaultAuthenticationFailureHandler
{
    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['success' => false, 'message' => $exception->getMessageKey()], 401);
        }

        return parent::onAuthenticationFailure($request, $exception);
    }
}
