<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AffiliateBundle\Form\EventListener;

use Sylius\Component\Affiliate\Model\ProvisionInterface;

/**
 * This listener adds configuration form to a provision,
 * if selected provision requires one.
 *
 */
class BuildProvisionFormSubscriber extends AbstractConfigurationSubscriber
{
    /**
     * Get provision configuration
     *
     * @param ProvisionInterface $provision
     *
     * @return array
     */
    protected function getConfiguration($provision)
    {
        if ($provision instanceof ProvisionInterface && null !== $provision->getConfiguration()) {
            return $provision->getConfiguration();
        }

        return array();
    }

    /**
     * Get provision type
     *
     * @param ProvisionInterface $provision
     *
     * @return null|string
     */
    protected function getRegistryIdentifier($provision)
    {
        if ($provision instanceof ProvisionInterface && null !== $provision->getType()) {
            return $provision->getType();
        }

        if (null !== $this->registryIdentifier) {
            return $this->registryIdentifier;
        }

        return null;
    }
}
