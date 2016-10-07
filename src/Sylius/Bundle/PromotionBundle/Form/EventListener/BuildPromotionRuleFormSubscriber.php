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

use Sylius\Component\Promotion\Model\PromotionRuleInterface;

/**
 * This listener adds configuration form to a rule,
 * if selected rule requires one.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
final class BuildPromotionRuleFormSubscriber extends AbstractConfigurationSubscriber
{
    /**
     * Get PromotionRule configuration
     *
     * @param PromotionRuleInterface $rule
     *
     * @return array
     */
    protected function getConfiguration($rule)
    {
        if ($rule instanceof PromotionRuleInterface && null !== $rule->getConfiguration()) {
            return $rule->getConfiguration();
        }

        return [];
    }

    /**
     * Get rule type
     *
     * @param PromotionRuleInterface $rule
     *
     * @return null|string
     */
    protected function getRegistryIdentifier($rule)
    {
        if ($rule instanceof PromotionRuleInterface && null !== $rule->getType()) {
            return $rule->getType();
        }

        if (null !== $this->registryIdentifier) {
            return $this->registryIdentifier;
        }

        return null;
    }
}
