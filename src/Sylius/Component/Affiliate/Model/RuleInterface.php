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

interface RuleInterface
{
    const TYPE_URI_VISIT     = 'uri_visit';
    const TYPE_NTH_ORDER     = 'nth_order';
    const TYPE_REGISTRATION  = 'registration';

    /**
     * Get associated goal.
     *
     * @return GoalInterface
     */
    public function getGoal();

    /**
     * Set associated goal.
     *
     * @param GoalInterface $goal
     */
    public function setGoal(GoalInterface $goal = null);

    /**
     * Get type.
     *
     * @return string
     */
    public function getType();

    /**
     * Set type.
     *
     * @param string $type
     */
    public function setType($type);

    /**
     * Get configuration.
     *
     * @return array
     */
    public function getConfiguration();

    /**
     * Set configuration.
     *
     * @param array $configuration
     */
    public function setConfiguration(array $configuration);
}
