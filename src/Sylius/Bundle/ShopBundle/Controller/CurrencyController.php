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

use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Provider\CurrencyProviderInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
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
     * @param EngineInterface $templatingEngine
     * @param CurrencyContextInterface $currencyContext
     * @param CurrencyProviderInterface $currencyProvider
     */
    public function __construct(
        EngineInterface $templatingEngine,
        CurrencyContextInterface $currencyContext,
        CurrencyProviderInterface $currencyProvider
    ) {
        $this->templatingEngine = $templatingEngine;
        $this->currencyContext = $currencyContext;
        $this->currencyProvider = $currencyProvider;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function renderSelectorAction(Request $request)
    {
        return $this->templatingEngine->renderResponse('SyliusShopBundle:Currency:selector.html.twig', [
            'activeCurrency' => $this->currencyContext->getCurrency(),
            'availableCurrencies' => $this->currencyProvider->getAvailableCurrencies(),
        ]);
    }
}
