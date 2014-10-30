<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CurrencyBundle\Context;

use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CurrencyContext implements CurrencyContextInterface
{
    const SESSION_KEY = '_sylius.currency';

    protected $session;
    protected $defaultCurrency;

    public function __construct(SessionInterface $session, $defaultCurrency)
    {
        $this->session = $session;
        $this->defaultCurrency = $defaultCurrency;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultCurrency()
    {
        return $this->defaultCurrency;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        if (!$this->session->isStarted()) {
            return $this->defaultCurrency;
        }

        return $this->session->get(self::SESSION_KEY, $this->defaultCurrency);
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrency($currency)
    {
        return $this->session->set(self::SESSION_KEY, $currency);
    }
}
