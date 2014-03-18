<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MoneyBundle\Context;

use Sylius\Component\Money\Context\CurrencyContextInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CurrencyContext implements CurrencyContextInterface
{
    protected $session;
    protected $defaultCurrency;

    public function __construct(SessionInterface $session, $defaultCurrency)
    {
        $this->session = $session;
        $this->defaultCurrency = $defaultCurrency;
    }

    public function getDefaultCurrency()
    {
        return $this->defaultCurrency;
    }

    public function getCurrency()
    {
        return $this->session->get('currency', $this->defaultCurrency);
    }

    public function setCurrency($currency)
    {
        return $this->session->set('currency', $currency);
    }
}
