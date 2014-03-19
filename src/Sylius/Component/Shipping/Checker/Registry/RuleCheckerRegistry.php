<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping\Checker\Registry;

use Sylius\Component\Shipping\Checker\RuleCheckerInterface;

/**
 * Rule checker registry.
 *
 * This service keeps all rule checkers registered inside
 * container. Allows to retrieve them by type.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class RuleCheckerRegistry implements RuleCheckerRegistryInterface
{
    /**
     * Rule checkers.
     *
     * @var RuleCheckerInterface[]
     */
    protected $checkers = array();

    /**
     * {@inheritdoc}
     */
    public function getCheckers()
    {
        return $this->checkers;
    }

    /**
     * {@inheritdoc}
     */
    public function registerChecker($name, RuleCheckerInterface $checker)
    {
        if ($this->hasChecker($name)) {
            throw new ExistingRuleCheckerException($name);
        }

        $this->checkers[$name] = $checker;
    }

    /**
     * {@inheritdoc}
     */
    public function unregisterChecker($name)
    {
        if (!$this->hasChecker($name)) {
            throw new NonExistingRuleCheckerException($name);
        }

        unset($this->checkers[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function hasChecker($name)
    {
        return isset($this->checkers[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getChecker($name)
    {
        if (!$this->hasChecker($name)) {
            throw new NonExistingRuleCheckerException($name);
        }

        return $this->checkers[$name];
    }
}
