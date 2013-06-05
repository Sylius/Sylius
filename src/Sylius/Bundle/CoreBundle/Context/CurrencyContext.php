<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Context;

use Sylius\Bundle\MoneyBundle\Context\CurrencyContext as BaseCurrencyContext;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CurrencyContext extends BaseCurrencyContext
{
    protected $securityContext;

    public function __construct(SecurityContextInterface $securityContext, SessionInterface $session, $defaultCurrency)
    {
        $this->securityContext = $securityContext;

        parent::__construct($session, $defaultCurrency);
    }

    public function getCurrency()
    {
        if ((null !== $token = $this->securityContext->getToken()) && is_object($user = $token->getUser())) {
            return $user->getCurrency();
        }


        return parent::getCurrency();
    }
}
