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
use Sylius\Bundle\UserBundle\Form\Model\PasswordResetRequest;
use Sylius\Bundle\UserBundle\Form\Type\UserRequestPasswordResetType;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

final class ResendVerificationEmailController
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly FormFactoryInterface $formFactory,
        private readonly Environment $twig,
        private readonly ChannelContextInterface $channelContext,
        private readonly LocaleContextInterface $localeContext,
        private readonly MessageBusInterface $commandBus,
    ) {
    }

    public function requestAction(Request $request): Response
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        if (!$channel->isAccountVerificationRequired()) {
            return new RedirectResponse($this->router->generate('sylius_shop_login'));
        }

        $form = $this->formFactory->create(UserRequestPasswordResetType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            /** @var PasswordResetRequest $data */
            $data = $form->getData();

            $this->commandBus->dispatch(new ResendVerificationEmail(
                channelCode: $channel->getCode(),
                localeCode: $this->localeContext->getLocaleCode(),
                email: (string) $data->getEmail(),
                sendVerificationLink: true,
            ));

            /** @var FlashBagInterface $flashBag */
            $flashBag = $request->getSession()->getBag('flashes');
            $flashBag->add('success', 'sylius.user.verification_email_sent');

            return new RedirectResponse($this->router->generate('sylius_shop_resend_verification_email'));
        }

        return new Response($this->twig->render(
            '@SyliusShop/account/resend_verification_email.html.twig',
            ['form' => $form->createView()],
        ));
    }
}
