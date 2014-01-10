<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Checker\Registry;

use Sylius\Bundle\ResourceBundle\Checker\RuleCheckerInterface;

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
     * @return RuleCheckerInterface[]
     */
    public function getCheckers()
    {
        return $this->checkers;
    }

    /**
     * @param string               $name
     * @param RuleCheckerInterface $checker
     *
     * @return RuleCheckerRegistryInterface
     * @throws ExistingRuleCheckerException
     */
    public function registerChecker($name, RuleCheckerInterface $checker)
    {
        if ($this->hasChecker($name)) {
            throw new ExistingRuleCheckerException($name);
        }

        $this->checkers[$name] = $checker;
    }

    /**
     * @param string $name
     *
     * @return RuleCheckerRegistryInterface
     * @throws NonExistingRuleCheckerException
     */
    public function unregisterChecker($name)
    {
        if (!$this->hasChecker($name)) {
            throw new NonExistingRuleCheckerException($name);
        }

        unset($this->checkers[$name]);
    }

    /**
     * @param string $name
     *
     * @return boolean
     */
    public function hasChecker($name)
    {
        return isset($this->checkers[$name]);
    }

    /**
     * @param string $name
     *
     * @return RuleCheckerInterface
     * @throws NonExistingRuleCheckerException
     */
    public function getChecker($name)
    {
        if (!$this->hasChecker($name)) {
            throw new NonExistingRuleCheckerException($name);
        }

        return $this->checkers[$name];
    }
}
