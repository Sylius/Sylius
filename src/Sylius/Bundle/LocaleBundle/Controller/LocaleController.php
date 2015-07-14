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

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;

/**
 * Locale controller.
 *
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
        $locale = $request->get('locale');

        if (!$this->getLocaleProvider()->isLocaleAvailable($locale)) {
            $locale = $this->getLocaleContext()->getDefaultLocale();
        }

        $this->getLocaleContext()->setLocale($locale);

        if ($this->config->isApiRequest()) {
            $view = $this
                ->view()
                ->setData(array('locale' => $locale))
            ;

            return $this->handleView($view);
        }

        return $this->redirect($request->headers->get('referer') ?: '/');
    }

    /**
     * @return LocaleContextInterface
     */
    protected function getLocaleContext()
    {
        return $this->get('sylius.context.locale');
    }

    /**
     * @return LocaleProviderInterface
     */
    protected function getLocaleProvider()
    {
        return $this->get('sylius.locale_provider');
    }
}
