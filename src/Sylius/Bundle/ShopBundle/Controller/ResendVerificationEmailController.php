<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ShopBundle\Controller;

use Sylius\Bundle\CoreBundle\Command\Account\ResendVerificationEmail;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final readonly class ResendVerificationEmailController
{
    public function __construct(
        private RouterInterface $router,
        private ChannelContextInterface $channelContext,
        private LocaleContextInterface $localeContext,
        private MessageBusInterface $commandBus,
        private AuthenticationUtils $authenticationUtils,
        private CsrfTokenManagerInterface $csrfTokenManager,
    ) {
    }

    public function resendAction(Request $request): Response
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();
        if (!$channel->isAccountVerificationRequired()) {
            return new RedirectResponse($this->router->generate('sylius_shop_login'));
        }

        $token = new CsrfToken('resend_verification_email', (string) $request->request->get('_csrf_token'));
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            return new RedirectResponse($this->router->generate('sylius_shop_login'));
        }

        $email = $this->authenticationUtils->getLastUsername();
        if ('' !== $email) {
            $this->commandBus->dispatch(new ResendVerificationEmail(
                channelCode: $channel->getCode(),
                localeCode: $this->localeContext->getLocaleCode(),
                email: $email,
                sendVerificationLink: true,
            ));
        }

        /** @var FlashBagInterface $flashBag */
        $flashBag = $request->getSession()->getBag('flashes');
        $flashBag->add('success', 'sylius.user.verification_email_sent');

        return new RedirectResponse($this->router->generate('sylius_shop_login'));
    }
}
