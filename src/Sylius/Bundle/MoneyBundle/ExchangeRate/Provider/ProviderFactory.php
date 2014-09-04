<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MoneyBundle\ExchangeRate\Provider;

use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Class ProviderFactory
 *
 * ProviderFactory responsibility is to create specific provider
 *
 * @author Ivan Djurdjevac <djurdjevac@gmail.com>
 */
class ProviderFactory extends ContainerAware
{

    /**
     * Create Exchange Rate Provider which is currently active
     * Currently active provider is saved in settings
     *
     * @return ProviderInterface
     */
    public function createProvider()
    {
        $providerKeyName = $this->container->get('sylius.exchange_rate.services')->getActiveProviderKey();

        return $this->container->get($providerKeyName);
    }
}
