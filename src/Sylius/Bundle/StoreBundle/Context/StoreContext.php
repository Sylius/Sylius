<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\StoreBundle\Context;

use Sylius\Component\Store\Context\StoreContextInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class StoreContext implements StoreContextInterface
{
    protected $session;
    protected $defaultStore;

    public function __construct(SessionInterface $session, $defaultStore)
    {
        $this->session = $session;
        $this->defaultStore = $defaultStore;
    }

    public function getDefaultStore()
    {
        return $this->defaultStore;
    }

    public function getStore()
    {
        return $this->session->get('currency', $this->defaultStore);
    }

    public function setStore($currency)
    {
        return $this->session->set('currency', $currency);
    }
}
