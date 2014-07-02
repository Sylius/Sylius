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
    public function changeAction(Request $request, $locale)
    {
        $this->getLocaleContext()->setLocale($locale);

        return $this->redirect($request->headers->get('referer'));
    }

    protected function getLocaleContext()
    {
        return $this->get('sylius.context.locale');
    }
}
