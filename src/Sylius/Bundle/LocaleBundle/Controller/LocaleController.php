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

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Locale controller.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class LocaleController extends ResourceController
{
    public function changeAction(Request $request)
    {
        $locale = $request->get('locale');

        if (!$this->isLocaleAvailable($locale)) {
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

    protected function getLocaleContext()
    {
        return $this->get('sylius.context.locale');
    }

    protected function getLocaleProvider()
    {
        return $this->get('sylius.locale_provider');
    }

    protected function isLocaleAvailable($locale)
    {
        return in_array($locale, $this->getLocaleProvider()->getLocales());
    }
}
