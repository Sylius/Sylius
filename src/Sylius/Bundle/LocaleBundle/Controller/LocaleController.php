<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\LocaleBundle\Controller;

use FOS\RestBundle\View\View;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class LocaleController extends ResourceController
{
    /**
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function changeAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $locale = $request->get('locale');

        if (!$this->getLocaleProvider()->isLocaleAvailable($locale)) {
            $locale = $this->getLocaleContext()->getDefaultLocale();
        }

        $this->getLocaleContext()->setCurrentLocale($locale);

        if (!$configuration->isHtmlRequest()) {
            $view = View::create(['locale' => $locale]);

            return $this->viewHandler->handle($configuration, $view);
        }

        return $this->redirectHandler->redirect($configuration, $request->headers->get('referer') ?: '/');
    }

    /**
     * @return LocaleContextInterface
     */
    protected function getLocaleContext()
    {
        return $this->container->get('sylius.context.locale');
    }

    /**
     * @return LocaleProviderInterface
     */
    protected function getLocaleProvider()
    {
        return $this->container->get('sylius.locale_provider');
    }
}
