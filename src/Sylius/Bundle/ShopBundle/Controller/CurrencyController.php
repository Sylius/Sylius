<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShopBundle\Controller;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Currency\CurrencyStorageInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Provider\CurrencyProviderInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class CurrencyController
{
    /**
     * @var EngineInterface
     */
    private $templatingEngine;

    /**
     * @var CurrencyContextInterface
     */
    private $currencyContext;

    /**
     * @var CurrencyProviderInterface
     */
    private $currencyProvider;

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @var CurrencyStorageInterface
     */
    private $currencyStorage;

    /**
     * @param EngineInterface $templatingEngine
     * @param CurrencyContextInterface $currencyContext
     * @param CurrencyProviderInterface $currencyProvider
     * @param ChannelContextInterface $channelContext
     * @param CurrencyStorageInterface $currencyStorage
     */
    public function __construct(
        EngineInterface $templatingEngine,
        CurrencyContextInterface $currencyContext,
        CurrencyProviderInterface $currencyProvider,
        ChannelContextInterface $channelContext,
        CurrencyStorageInterface $currencyStorage
    ) {
        $this->templatingEngine = $templatingEngine;
        $this->currencyContext = $currencyContext;
        $this->currencyProvider = $currencyProvider;
        $this->channelContext = $channelContext;
        $this->currencyStorage = $currencyStorage;
    }

    /**
     * @return Response
     */
    public function renderSelectorAction()
    {
        return $this->templatingEngine->renderResponse('SyliusShopBundle:Currency:selector.html.twig', [
            'activeCurrencyCode' => $this->currencyContext->getCurrencyCode(),
            'availableCurrenciesCodes' => $this->currencyProvider->getAvailableCurrenciesCodes(),
        ]);
    }

    /**
     * @param Request $request
     * @param string $code
     *
     * @return Response
     */
    public function switchCurrencyAction(Request $request, $code)
    {
        $this->currencyStorage->set($this->channelContext->getChannel(), $code);

        return new RedirectResponse($request->headers->get('referer'));
    }
}
