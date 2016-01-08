<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionBundle\Form\EventListener;

use Sylius\Component\Promotion\Model\ActionInterface;

/**
 * This listener adds configuration form to a action,
 * if selected action requires one.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class BuildActionFormSubscriber extends AbstractConfigurationSubscriber
{
    /**
     * Get action configuration
     *
     * @param ActionInterface $action
     *
     * @return array
     */
    protected function getConfiguration($action)
    {
        if ($action instanceof ActionInterface && null !== $action->getConfiguration()) {
            return $action->getConfiguration();
        }

        return [];
    }

    /**
     * Get action type
     *
     * @param ActionInterface $action
     *
     * @return null|string
     */
    protected function getRegistryIdentifier($action)
    {
        if ($action instanceof ActionInterface && null !== $action->getType()) {
            return $action->getType();
        }

        if (null !== $this->registryIdentifier) {
            return $this->registryIdentifier;
        }

        return null;
    }
}
