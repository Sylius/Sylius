<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Checker\Registry;

use Sylius\Bundle\ShippingBundle\Checker\RuleCheckerInterface;

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
    protected $checkers;

    public function __construct()
    {
        $this->checkers = array();
    }

    public function getCheckers()
    {
        return $this->checkers;
    }

    public function registerChecker($name, RuleCheckerInterface $checker)
    {
        if ($this->hasChecker($name)) {
            throw new ExistingRuleCheckerException($name);
        }

        $this->checkers[$name] = $checker;
    }

    public function unregisterChecker($name)
    {
        if (!$this->hasChecker($name)) {
            throw new NonExistingRuleCheckerException($name);
        }

        unset($this->checkers[$name]);
    }

    public function hasChecker($name)
    {
        return isset($this->checkers[$name]);
    }

    public function getChecker($name)
    {
        if (!$this->hasChecker($name)) {
            throw new NonExistingRuleCheckerException($name);
        }

        return $this->checkers[$name];
    }
}
