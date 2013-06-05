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
use Doctrine\Common\Persistence\ObjectManager;

class CurrencyContext extends BaseCurrencyContext
{
    protected $securityContext;
    protected $userManager;

    public function __construct(SecurityContextInterface $securityContext, SessionInterface $session, ObjectManager $userManager, $defaultCurrency)
    {
        $this->securityContext = $securityContext;
        $this->userManager = $userManager;

        parent::__construct($session, $defaultCurrency);
    }

    public function getCurrency()
    {
        if (null === $user = $this->getUser()) {
            return parent::getCurrency();
        }

        return $user->getCurrency();
    }

    public function setCurrency($currency)
    {
        if (null === $user = $this->getUser()) {
            return parent::setCurrency($currency);
        }

        $user->setCurrency($currency);

        $this->userManager->persist($user);
        $this->userManager->flush();
    }

    protected function getUser()
    {
        if ((null !== $token = $this->securityContext->getToken()) && is_object($user = $token->getUser())) {
            return $user;
        }
    }
}
