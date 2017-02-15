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

use Sylius\Bundle\ShopBundle\Locale\LocaleSwitcherInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class LocaleSwitchController
{
    /**
     * @var EngineInterface
     */
    private $templatingEngine;

    /**
     * @var LocaleContextInterface
     */
    private $localeContext;

    /**
     * @var LocaleProviderInterface
     */
    private $localeProvider;

    /**
     * @var LocaleSwitcherInterface
     */
    private $localeSwitcher;

    /**
     * @param EngineInterface $templatingEngine
     * @param LocaleContextInterface $localeContext
     * @param LocaleProviderInterface $localeProvider
     * @param LocaleSwitcherInterface $localeSwitcher
     */
    public function __construct(
        EngineInterface $templatingEngine,
        LocaleContextInterface $localeContext,
        LocaleProviderInterface $localeProvider,
        LocaleSwitcherInterface $localeSwitcher
    ) {
        $this->templatingEngine = $templatingEngine;
        $this->localeContext = $localeContext;
        $this->localeProvider = $localeProvider;
        $this->localeSwitcher = $localeSwitcher;
    }

    /**
     * @return Response
     */
    public function renderAction()
    {
        return $this->templatingEngine->renderResponse('@SyliusShop/Menu/_localeSwitch.html.twig', [
            'active' => $this->localeContext->getLocaleCode(),
            'locales' => $this->localeProvider->getAvailableLocalesCodes(),
        ]);
    }

    /**
     * @param Request $request
     * @param string $code
     *
     * @return Response
     */
    public function switchAction(Request $request, $code = null)
    {
        if (null === $code) {
            $code = $this->localeProvider->getDefaultLocaleCode();
        }

        if (!in_array($code, $this->localeProvider->getAvailableLocalesCodes(), true)) {
            throw new HttpException(
                Response::HTTP_NOT_ACCEPTABLE,
                sprintf('The locale code "%s" is invalid.', $code)
            );
        }

        return $this->localeSwitcher->handle($request, $code);
    }
}
