<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CurrencyBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CurrencyController extends ResourceController
{
    public function changeAction(Request $request, $currency)
    {
        $this->getCurrencyContext()->setCurrency($currency);

        return $this->redirect($request->headers->get('referer'));
    }

    protected function getCurrencyContext()
    {
        return $this->get('sylius.context.currency');
    }
}
