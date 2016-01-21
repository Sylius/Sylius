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
use Sylius\Component\Currency\Context\CurrencyContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CurrencyController extends ResourceController
{
    /**
     * @param Request $request
     * @param string  $currency
     *
     * @return RedirectResponse
     */
    public function changeAction(Request $request, $currency)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->getCurrencyContext()->setCurrency($currency);

        return $this->redirectHandler->redirectToReferer($configuration);
    }

    /**
     * @return CurrencyContext
     */
    protected function getCurrencyContext()
    {
        return $this->container->get('sylius.context.currency');
    }
}
