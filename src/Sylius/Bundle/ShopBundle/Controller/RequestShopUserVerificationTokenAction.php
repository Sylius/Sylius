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

use Sylius\Bundle\CoreBundle\Command\Shop\Account\RequestShopUserVerification;
use Sylius\Bundle\CoreBundle\Provider\FlashBagProvider;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @experimental
 */
final readonly class RequestShopUserVerificationTokenAction
{
    public function __construct(
        private RouterInterface $router,
        private TokenStorageInterface $tokenStorage,
        private RequestStack $requestStack,
        private ChannelContextInterface $channelContext,
        private LocaleContextInterface $localeContext,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(Request $request, ?string $redirect): Response
    {
        $redirect ??= 'sylius_shop_account_dashboard';

        $user = $this->getUser();

        if (null === $user) {
            $this->addWarningNotification('sylius.user.verify_no_user');

            return new RedirectResponse($this->router->generate($redirect));
        }

        if (null !== $user->getVerifiedAt()) {
            $this->addWarningNotification('sylius.user.verify_verified_email');

            return new RedirectResponse($this->router->generate($redirect));
        }

        $this->messageBus->dispatch(new RequestShopUserVerification(
            $user->getId(),
            $this->channelContext->getChannel()->getCode(),
            $this->localeContext->getLocaleCode(),
        ));

        FlashBagProvider::getFlashBag($this->requestStack)->add('success', 'sylius.user.verify_email_request');

        return new RedirectResponse($this->router->generate($redirect));
    }

    private function addWarningNotification(string $message): void
    {
        FlashBagProvider::getFlashBag($this->requestStack)->add('warning', $message);
    }

    private function getUser(): ?UserInterface
    {
        /** @var UserInterface|null $user */
        $user = $this->tokenStorage->getToken()?->getUser();

        if (null === $user) {
            return null;
        }

        return $user;
    }
}
