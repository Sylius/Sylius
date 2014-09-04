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
use Sylius\Bundle\MoneyBundle\ExchangeRate\Provider\ProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CurrencyController extends Controller
{
    public function changeAction(Request $request, $currency)
    {
        $this->get('sylius.currency_context')->setCurrency($currency);

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Update all exchange rates with Database Updater
     * @param  Request                                            $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function updateAllRatesAction(Request $request)
    {
        $databaseUpdater = $this->container->get('sylius.exchange_rate.updater');

        try {
            if ($databaseUpdater->updateAllRates()) {
                $message = $this->get('translator')->trans('sylius.exchange_rate.update.success', array(), 'flashes');
                $request->getSession()->getFlashBag()->add('success', $message);
            } else {
                $message = $this->get('translator')->trans('sylius.exchange_rate.update.error', array(), 'flashes');
                $request->getSession()->getFlashBag()->add('error', $message);
            }
        } catch (ProviderException $exception) {
            $message = $this->get('translator')->trans('sylius.exchange_rate.update.provider_exception', array(), 'flashes');
            $request->getSession()->getFlashBag()->add('error', $message);
        }

        return $this->redirect($this->get('router')->generate('sylius_backend_exchange_rate_index'));
    }

    public function settingsAction(Request $request)
    {
        return $this->render($request->attributes->get('template', 'SyliusWebBundle:Backend/ExchangeRate:settings/settings.html.twig'), array(

        ));
    }

    public function saveSettingsAction()
    {

    }
}
