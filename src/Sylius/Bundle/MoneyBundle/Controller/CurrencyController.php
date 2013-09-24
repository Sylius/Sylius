<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MoneyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CurrencyController extends Controller
{
    public function changeAction(Request $request, $currency)
    {
        $this->get('sylius.currency_context')->setCurrency($currency);

        return $this->redirect($request->headers->get('referer'));
    }
}
