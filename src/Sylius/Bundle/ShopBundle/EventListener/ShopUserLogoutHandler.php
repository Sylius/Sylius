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

namespace Sylius\Bundle\ShopBundle\EventListener;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Storage\CartStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\Security\Http\HttpUtils;

final class ShopUserLogoutHandler
{
    public function __construct(
        private HttpUtils $httpUtils,
        private string $targetUrl,
        private ChannelContextInterface $channelContext,
        private CartStorageInterface $cartStorage,
        private TokenStorageInterface $tokenStorage,
    ) {
    }

    public function onLogout(LogoutEvent $logoutEvent): void
    {
        if ($logoutEvent->getResponse() !== null) {
            return;
        }

        $channel = $this->channelContext->getChannel();
        $this->cartStorage->removeForChannel($channel);

        $this->tokenStorage->setToken(null);

        $response = $this->httpUtils->createRedirectResponse($logoutEvent->getRequest(), $this->targetUrl);
        $logoutEvent->setResponse($response);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => [['onLogout', 64]],
        ];
    }
}
