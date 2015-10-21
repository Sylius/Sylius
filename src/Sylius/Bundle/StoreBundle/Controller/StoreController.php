<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\StoreBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StoreController extends ResourceController
{
    /**
     * @param Request $request
     * @param string  $currency
     *
     * @return RedirectResponse
     */
    public function changeAction(Request $request, $currency)
    {
        $this->getStoreContext()->setStore($currency);

        return $this->redirect($request->headers->get('referer'));
    }
}
