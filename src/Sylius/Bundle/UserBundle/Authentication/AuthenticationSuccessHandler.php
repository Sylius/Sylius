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
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;

final class AuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler
{
    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['success' => true, 'username' => $token->getUsername()]);
        }

        return parent::onAuthenticationSuccess($request, $token);
    }
}
