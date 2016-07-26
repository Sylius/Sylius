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
use Sylius\Component\Core\Locale\LocaleStorageInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class LocaleController
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
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @var LocaleStorageInterface
     */
    private $localeStorage;

    /**
     * @param EngineInterface $templatingEngine
     * @param LocaleContextInterface $localeContext
     * @param LocaleProviderInterface $localeProvider
     * @param ChannelContextInterface $channelContext
     * @param LocaleStorageInterface $localeStorage
     */
    public function __construct(
        EngineInterface $templatingEngine,
        LocaleContextInterface $localeContext,
        LocaleProviderInterface $localeProvider,
        ChannelContextInterface $channelContext,
        LocaleStorageInterface $localeStorage
    ) {
        $this->templatingEngine = $templatingEngine;
        $this->localeContext = $localeContext;
        $this->localeProvider = $localeProvider;
        $this->channelContext = $channelContext;
        $this->localeStorage = $localeStorage;
    }

    /**
     * @return Response
     */
    public function renderSelectorAction()
    {
        return $this->templatingEngine->renderResponse('SyliusShopBundle:Locale:selector.html.twig', [
            'activeLocaleCode' => $this->localeContext->getLocaleCode(),
            'availableLocalesCodes' => $this->localeProvider->getAvailableLocalesCodes(),
        ]);
    }

    /**
     * @param Request $request
     * @param string $code
     *
     * @return Response
     */
    public function switchLocaleAction(Request $request, $code)
    {
        $this->localeStorage->set($this->channelContext->getChannel(), $code);

        return new RedirectResponse($request->headers->get('referer'));
    }
}
