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

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

final class RegistrationThankYouController
{
    public function __construct(
        private Environment $twig,
        private ChannelContextInterface $channelContext,
        private RouterInterface $router,
    ) {
    }

    public function thankYouAction(): Response
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        if ($channel->isAccountVerificationRequired()) {
            return new Response($this->twig->render('@SyliusShop/registerThankYou.html.twig'));
        }

        return new RedirectResponse($this->router->generate('sylius_shop_account_dashboard'));
    }
}
