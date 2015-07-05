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

use Sylius\Bundle\CoreBundle\Locale\ChannelAwareLocaleProvider;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Locale\Context\LocaleContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @return LocaleContext
     */
    protected function getLocaleContext()
    {
        return $this->get('sylius.context.locale');
    }

    /**
     * @return ChannelAwareLocaleProvider
     */
    protected function getLocaleProvider()
    {
        return $this->get('sylius.locale_provider');
    }
}
