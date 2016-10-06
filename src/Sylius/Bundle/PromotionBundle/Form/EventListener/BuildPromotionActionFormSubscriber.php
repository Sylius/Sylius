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

use Sylius\Component\Promotion\Model\PromotionActionInterface;

/**
 * This listener adds configuration form to a action,
 * if selected action requires one.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
final class BuildPromotionActionFormSubscriber extends AbstractConfigurationSubscriber
{
    /**
     * Get action configuration
     *
     * @param PromotionActionInterface $action
     *
     * @return array
     */
    protected function getConfiguration($action)
    {
        if ($action instanceof PromotionActionInterface && null !== $action->getConfiguration()) {
            return $action->getConfiguration();
        }

        return [];
    }

    /**
     * Get action type
     *
     * @param PromotionActionInterface $action
     *
     * @return null|string
     */
    protected function getRegistryIdentifier($action)
    {
        if ($action instanceof PromotionActionInterface && null !== $action->getType()) {
            return $action->getType();
        }

        if (null !== $this->registryIdentifier) {
            return $this->registryIdentifier;
        }

        return null;
    }
}
