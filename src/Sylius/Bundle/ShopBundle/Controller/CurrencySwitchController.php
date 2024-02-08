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
use Sylius\Component\Core\Currency\CurrencyStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class CurrencySwitchController
{
    public function __construct(
        private Environment $templatingEngine,
        private CurrencyContextInterface $currencyContext,
        private CurrencyStorageInterface $currencyStorage,
        private ChannelContextInterface $channelContext,
    ) {
    }

    public function renderAction(): Response
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        $availableCurrencies = array_map(
            fn (CurrencyInterface $currency) => $currency->getCode(),
            $channel->getCurrencies()->toArray(),
        );

        return new Response($this->templatingEngine->render('@SyliusShop/Menu/_currencySwitch.html.twig', [
            'active' => $this->currencyContext->getCurrencyCode(),
            'currencies' => $availableCurrencies,
        ]));
    }

    public function switchAction(Request $request, string $code): Response
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        $this->currencyStorage->set($channel, $code);

        return new RedirectResponse($request->headers->get('referer', $request->getSchemeAndHttpHost()));
    }
}
