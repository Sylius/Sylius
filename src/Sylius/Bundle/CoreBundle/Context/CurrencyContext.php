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

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\CurrencyBundle\Context\CurrencyContext as BaseCurrencyContext;
use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class CurrencyContext extends BaseCurrencyContext
{
    protected $securityContext;
    protected $settingsManager;
    protected $userManager;

    public function __construct(
        SessionInterface $session,
        SecurityContextInterface $securityContext,
        SettingsManagerInterface $settingsManager,
        ObjectManager $userManager
    ) {
        $this->securityContext = $securityContext;
        $this->settingsManager = $settingsManager;
        $this->userManager = $userManager;

        parent::__construct($session, $this->getDefaultCurrency());
    }

    public function getDefaultCurrency()
    {
        return $this->settingsManager->loadSettings('general')->get('currency');
    }

    public function getCurrency()
    {
        if ((null !== $user = $this->getUser()) && null !== $user->getCurrency()) {
            return $user->getCurrency();
        }

        return parent::getCurrency();
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
        if ($this->securityContext->getToken() && $this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->securityContext->getToken()->getUser();
        }
    }
}
