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
use Symfony\Contracts\Translation\TranslatorInterface;

final class AuthenticationFailureHandler extends DefaultAuthenticationFailureHandler
{
    /** @var ?TranslatorInterface */
    private $translator;

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        if ($this->translator === null) {
            trigger_deprecation(
                'sylius/sylius',
                '1.9.5',
                'Not setting a translator to %s is deprecated and will be removed in %s.',
                self::class,
                '2.0'
            );
        }
        if ($request->isXmlHttpRequest()) {
            $message = $exception->getMessageKey();
            if ($this->translator !== null) {
                $message = $this->translator->trans($message, [], 'security');
            }

            return new JsonResponse(['success' => false, 'message' => $message], 401);
        }

        return parent::onAuthenticationFailure($request, $exception);
    }

    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }
}
