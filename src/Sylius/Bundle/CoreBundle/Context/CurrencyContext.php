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
use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Sylius\Component\Currency\Context\CurrencyContext as BaseCurrencyContext;
use Sylius\Component\Customer\Model\CustomerAwareInterface;
use Sylius\Component\Storage\StorageInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class CurrencyContext extends BaseCurrencyContext
{
    protected $securityContext;
    protected $settingsManager;
    protected $userManager;

    public function __construct(
        StorageInterface $storage,
        SecurityContextInterface $securityContext,
        SettingsManagerInterface $settingsManager,
        ObjectManager $userManager
    ) {
        $this->securityContext = $securityContext;
        $this->settingsManager = $settingsManager;
        $this->userManager = $userManager;

        parent::__construct($storage, $this->getDefaultCurrency());
    }

    public function getDefaultCurrency()
    {
        return $this->settingsManager->loadSettings('general')->get('currency');
    }

    public function getCurrency()
    {
        $user = $this->getUser();
        if (null !== $user) {
            if ($user instanceof CustomerAwareInterface) {
                $user = $user->getCustomer();
            }

            if (null !== $user->getCurrency()) {
                return $user->getCurrency();
            }
        }

        return parent::getCurrency();
    }

    public function setCurrency($currency)
    {
        $user = $this->getUser();
        if (null !== $user) {
            if ($user instanceof CustomerAwareInterface) {
                $user = $user->getCustomer();
            }

            $user->setCurrency($currency);

            $this->userManager->persist($user);
            $this->userManager->flush();
        }

        return parent::setCurrency($currency);
    }

    protected function getUser()
    {
        if ($this->securityContext->isGranted('IS_CUSTOMER') || $this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->securityContext->getToken()->getUser();
        }
    }
}
