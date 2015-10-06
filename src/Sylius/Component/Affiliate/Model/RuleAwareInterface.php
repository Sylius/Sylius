<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Affiliate\Model;

use Doctrine\Common\Collections\Collection;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
interface RuleAwareInterface
{
    /**
     * Get all assigned rules.
     *
     * @return Collection|RuleInterface[]
     */
    public function getRules();

    /**
     * Check if it contains rules?
     *
     * @return bool
     */
    public function hasRules();

    /**
     * Check if it contains the rule?
     *
     * @param RuleInterface $rule
     *
     * @return bool
     */
    public function hasRule(RuleInterface $rule);

    /**
     * Adds rule.
     *
     * @param RuleInterface $rule
     */
    public function addRule(RuleInterface $rule);

    /**
     * Remove rule.
     *
     * @param RuleInterface $rule
     */
    public function removeRule(RuleInterface $rule);
}
