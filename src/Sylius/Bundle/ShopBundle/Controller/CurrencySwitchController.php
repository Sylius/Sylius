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

namespace Sylius\Bundle\ShopBundle\Controller;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Currency\CurrencyStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CurrencySwitchController
{
    /** @var EngineInterface */
    private $templatingEngine;

    /** @var CurrencyContextInterface */
    private $currencyContext;

    /** @var CurrencyStorageInterface */
    private $currencyStorage;

    /** @var ChannelContextInterface */
    private $channelContext;

    public function __construct(
        EngineInterface $templatingEngine,
        CurrencyContextInterface $currencyContext,
        CurrencyStorageInterface $currencyStorage,
        ChannelContextInterface $channelContext
    ) {
        $this->templatingEngine = $templatingEngine;
        $this->currencyContext = $currencyContext;
        $this->currencyStorage = $currencyStorage;
        $this->channelContext = $channelContext;
    }

    public function renderAction(): Response
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        $availableCurrencies = array_map(
            function (CurrencyInterface $currency) {
                return $currency->getCode();
            },
            $channel->getCurrencies()->toArray()
        );

        return $this->templatingEngine->renderResponse('@SyliusShop/Menu/_currencySwitch.html.twig', [
            'active' => $this->currencyContext->getCurrencyCode(),
            'currencies' => $availableCurrencies,
        ]);
    }

    public function switchAction(Request $request, string $code): Response
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        $this->currencyStorage->set($channel, $code);

        return new RedirectResponse($request->headers->get('referer', $request->getSchemeAndHttpHost()));
    }
}
