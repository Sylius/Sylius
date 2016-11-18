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

use Sylius\Component\Core\Currency\Handler\CurrencyChangeHandlerInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Provider\CurrencyProviderInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class CurrencySwitchController
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
     * @var CurrencyChangeHandlerInterface
     */
    private $currencyChangeHandler;

    /**
     * @param EngineInterface $templatingEngine
     * @param CurrencyContextInterface $currencyContext
     * @param CurrencyProviderInterface $currencyProvider
     * @param CurrencyChangeHandlerInterface $currencyChangeHandler
     */
    public function __construct(
        EngineInterface $templatingEngine,
        CurrencyContextInterface $currencyContext,
        CurrencyProviderInterface $currencyProvider,
        CurrencyChangeHandlerInterface $currencyChangeHandler
    ) {
        $this->templatingEngine = $templatingEngine;
        $this->currencyContext = $currencyContext;
        $this->currencyProvider = $currencyProvider;
        $this->currencyChangeHandler = $currencyChangeHandler;
    }

    /**
     * @return Response
     */
    public function renderAction()
    {
        return $this->templatingEngine->renderResponse('@SyliusShop/_currencySwitch.html.twig', [
            'active' => $this->currencyContext->getCurrencyCode(),
            'currencies' => $this->currencyProvider->getAvailableCurrenciesCodes(),
        ]);
    }

    /**
     * @param Request $request
     * @param string $code
     *
     * @return Response
     */
    public function switchAction(Request $request, $code)
    {
        if (!in_array($code, $this->currencyProvider->getAvailableCurrenciesCodes())) {
            throw new HttpException(
                Response::HTTP_NOT_ACCEPTABLE,
                sprintf('The currency code "%s" is invalid.', $code)
            );
        }

        $this->currencyChangeHandler->handle($code);

        return new RedirectResponse($request->headers->get('referer', $request->getSchemeAndHttpHost()));
    }
}
